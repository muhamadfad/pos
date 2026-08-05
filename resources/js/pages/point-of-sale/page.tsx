import React, { useState, useRef, useEffect, useMemo } from "react";
import { router, usePage } from "@inertiajs/react";
import type { PageProps, Product, CartItem } from "./types";
import Header from "./Header";
import ProductGrid from "./ProductGrid";
import CartPanel from "./CartPanel";
import DebtPaymentPanel from "./DebtPaymentPanel";
import { v4 as uuidv4 } from "uuid";
import { db } from "../../lib/db";
import { usePosSync } from "../../hooks/usePosSync";
import { useWebBluetoothPrinter } from "../../hooks/useWebBluetoothPrinter";
import ReceiptPrint, { TransactionDetails } from "./ReceiptPrint";
import TransactionHistoryModal from "./TransactionHistoryModal";
import { printWithRawBT } from "./printHelper";

export default function PointOfSalePage() {
    const {
        authUser,
        products,
        categories,
        customers: serverCustomers,
        customerDebts,
        activeShift,
        flash,
        errors,
    } = usePage<PageProps>().props;

    const { isOnline, pendingCount, isSyncing, processOfflineCheckout } = usePosSync();

    const [customers, setCustomers] = useState(serverCustomers);

    // Load offline customers from IndexedDB
    useEffect(() => {
        const loadOfflineCustomers = async () => {
            try {
                const localCusts = await db.customers.toArray();
                const mappedLocalCusts = localCusts.map(c => ({
                    id: c.id,
                    name: c.name,
                    phone: c.phone,
                    type: "retail",
                }));
                // Combine server customers and offline customers
                // Ensure no duplicates if they happen to share IDs (though offline is negative)
                setCustomers((prev) => {
                    const existingIds = new Set(prev.map(p => p.id));
                    const newCusts = mappedLocalCusts.filter(lc => !existingIds.has(lc.id));
                    return [...prev, ...newCusts];
                });
            } catch (error) {
                console.error("Failed to load offline customers", error);
            }
        };
        loadOfflineCustomers();
    }, []);

    // Mode: "pos" = transaksi baru, "debt" = bayar kasbon
    const [mode, setMode] = useState<"pos" | "debt">("pos");
    const [showMobileCart, setShowMobileCart] = useState(false);

    
    const { isConnected: isBluetoothConnected, printReceipt: printBluetooth } = useWebBluetoothPrinter();

    const [categoryFilter, setCategoryFilter] = useState<number | null>(null);
    const [cart, setCart] = useState<CartItem[]>([]);
    const [search, setSearch] = useState("");
    const [paid, setPaid] = useState<string>("");
    const [selectedCustomerId, setSelectedCustomerId] = useState<number | null>(
        null,
    );
    const [showSuccess, setShowSuccess] = useState(false);
    const [processing, setProcessing] = useState(false);
    const [lastTransaction, setLastTransaction] = useState<TransactionDetails | null>(null);
    const [showHistoryModal, setShowHistoryModal] = useState(false);
    
    // Custom item state
    const [showCustomItemModal, setShowCustomItemModal] = useState(false);
    const [customItemName, setCustomItemName] = useState("");
    const [customItemPrice, setCustomItemPrice] = useState("");

    const searchRef = useRef<HTMLInputElement>(null);
    const paidRef = useRef<HTMLInputElement>(null);
    const customNameRef = useRef<HTMLInputElement>(null);

    useEffect(() => {
        searchRef.current?.focus();
    }, []);

    useEffect(() => {
        if (flash?.success) {
            setShowSuccess(true);
            setCart([]);
            setPaid("");
            setSelectedCustomerId(null);
            searchRef.current?.focus();
            const timer = setTimeout(() => setShowSuccess(false), 5000);
            return () => clearTimeout(timer);
        }
    }, [flash?.success]);

    const filteredProducts = useMemo(() => {
        return products.filter((p) => {
            const matchSearch =
                search === "" ||
                p.name.toLowerCase().includes(search.toLowerCase()) ||
                (p.barcode && p.barcode.toLowerCase().includes(search.toLowerCase()));
            const matchCategory =
                categoryFilter === null || p.category_id === categoryFilter;
            return matchSearch && matchCategory;
        });
    }, [products, search, categoryFilter]);

    const totalItems = cart.reduce((sum, item) => sum + item.qty, 0);
    const totalPrice = cart.reduce(
        (sum, item) => sum + item.price * item.qty,
        0,
    );
    const paidAmount = parseFloat(paid) || 0;
    const change = paidAmount - totalPrice;

    function addToCart(product: Product) {
    setCart((prev) => {
        const existing = prev.find((c) => c.product_id === product.id);
        if (existing) {
            return prev.map((c) =>
                c.product_id === product.id ? { ...c, qty: c.qty + 1 } : c,
            );
        }
        return [
            ...prev,
            {
                product_id: product.id,
                barcode: product.barcode,
                name: product.name,
                price: product.price,
                qty: 1,
            },
        ];
    });
}

function addCustomItem() {
    if (!customItemName.trim()) {
        alert("Nama item tidak boleh kosong");
        customNameRef.current?.focus();
        return;
    }
    const price = parseInt(customItemPrice) || 0;
    
    setCart((prev) => [
        ...prev,
        {
            product_id: -Date.now(), // Fake ID for UI logic
            barcode: null,
            name: customItemName.trim(),
            price: price,
            qty: 1,
            is_custom: true, // Marker for backend
        },
    ]);
    
    setShowCustomItemModal(false);
    setCustomItemName("");
    setCustomItemPrice("");
}

function updateQty(productId: number, qty: number) {
    if (qty < 1) {
        removeFromCart(productId);
        return;
    }
    setCart((prev) =>
        prev.map((c) =>
            c.product_id === productId ? { ...c, qty: qty } : c,
        ),
    );
}

    function removeFromCart(productId: number) {
        setCart((prev) => prev.filter((c) => c.product_id !== productId));
    }

    function clearCart() {
        setCart([]);
        setPaid("");
        setSelectedCustomerId(null);
        searchRef.current?.focus();
    }

    function handleSearchKeyDown(e: React.KeyboardEvent<HTMLInputElement>) {
        if (e.key === "Enter" && search.trim()) {
            const product = products.find(
                (p) => p.barcode && p.barcode.toLowerCase() === search.trim().toLowerCase(),
            );
            if (product) {
                addToCart(product);
                setSearch("");
            }
        }
    }

    async function handleCreateCustomer(name: string) {
        try {
            // Generate a negative ID for offline customers
            const offlineId = -Date.now();
            const clientUuid = uuidv4();
            
            const newCust = {
                id: offlineId,
                name: name,
                phone: null,
                is_synced: false,
            };

            // 1. Save to local customers store
            await db.customers.put(newCust);
            
            // 2. Queue for sync
            await db.sync_queue.put({
                client_uuid: clientUuid,
                type: "customer",
                status: "pending",
                retry_count: 0,
                payload: {
                    client_uuid: clientUuid,
                    offline_id: offlineId,
                    name: name,
                    phone: null,
                },
            });

            // 3. Update local state
            setCustomers((prev) => [...prev, {
                id: offlineId,
                name: name,
                type: "retail",
                phone: null,
            }]);

            // 4. Select the new customer
            setSelectedCustomerId(offlineId);

        } catch (error) {
            console.error("Failed to create offline customer", error);
            alert("Gagal membuat pelanggan offline.");
        }
    }

    function handleCheckout(
        paymentType: "lunas" | "partial" | "kasbon",
        paymentMethod: "cash" | "transfer",
    ) {
        if (cart.length === 0) return;

        // Validasi per tipe
        if (paymentType === "lunas" && paidAmount < totalPrice) {
            paidRef.current?.focus();
            return;
        }
        if (
            paymentType === "partial" &&
            (paidAmount <= 0 || paidAmount >= totalPrice)
        ) {
            paidRef.current?.focus();
            return;
        }

        // kasbon: paid = 0
        const actualPaid = paymentType === "kasbon" ? 0 : paidAmount;

        const clientUuid = uuidv4();
        const payload = {
            client_uuid: clientUuid,
            branch_id: authUser.branch_id,
            cashier_shift_id: activeShift?.id,
            user_id: authUser.id,
            items: cart.map((c) => ({
                product_id: c.product_id,
                qty: c.qty,
                price: c.price,
                is_custom: c.is_custom,
                custom_name: c.is_custom ? c.name : undefined,
            })),
            paid: actualPaid,
            customer_id: selectedCustomerId ?? undefined,
            payment_method: paymentMethod,
            payment_type: paymentType,
        };

        const processOfflineCheckout = async () => {
            setProcessing(true);
            try {
                await db.sync_queue.put({
                    client_uuid: clientUuid,
                    type: "order",
                    status: "pending",
                    retry_count: 0,
                    payload: payload,
                });
                
                const customerName = customers.find((c) => c.id === selectedCustomerId)?.name || "Walk-in";
                const transactionData = {
                    cart: [...cart],
                    totalPrice,
                    paidAmount: actualPaid,
                    change: actualPaid - totalPrice,
                    paymentType,
                    date: new Date().toLocaleString("id-ID"),
                    customerName: customerName,
                    cashierName: authUser.name,
                };
                
                setLastTransaction(transactionData);
                
                // Show success immediately (optimistic update)
                setShowSuccess(true);
                setCart([]);
                setPaid("");
                setSelectedCustomerId(null);
                searchRef.current?.focus();
                
                // Auto-print receipt after a short delay
                setTimeout(() => {
                    if (isBluetoothConnected) {
                        printBluetooth(transactionData);
                    } else if (window.navigator.userAgent.toLowerCase().includes("android")) {
                        printWithRawBT(transactionData);
                    } else {
                        window.print();
                    }
                }, 500);
                
                // Hide success message after 5 seconds
                setTimeout(() => setShowSuccess(false), 5000);
            } catch (error) {
                console.error("Failed to save order to indexedDB", error);
                alert("Gagal menyimpan pesanan ke database lokal.");
            } finally {
                setProcessing(false);
            }
        };

        processOfflineCheckout();
    }

    const formatRupiah = (num: number) =>
        new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(num);

    return (
        <>
            <ReceiptPrint transaction={lastTransaction} />
            <TransactionHistoryModal 
                isOpen={showHistoryModal} 
                onClose={() => setShowHistoryModal(false)} 
                onReprint={(trx) => {
                    setLastTransaction(trx);
                    setTimeout(() => {
                        if (isBluetoothConnected) {
                            printBluetooth(trx);
                        } else if (window.navigator.userAgent.toLowerCase().includes("android")) {
                            printWithRawBT(trx);
                        } else {
                            window.print();
                        }
                    }, 200);
                }} 
            />
            <div className="flex h-screen bg-[#f8fafc] overflow-hidden relative print:hidden">
                {/* LEFT: Product Grid — hanya tampil saat mode pos */}
                {mode === "pos" && (
                    <div className={`flex-1 flex-col min-w-0 ${showMobileCart ? 'hidden lg:flex' : 'flex'}`}>
                        <Header
                            search={search}
                            onSearchChange={setSearch}
                            onSearchKeyDown={handleSearchKeyDown}
                            searchRef={searchRef}
                            categoryFilter={categoryFilter}
                            onCategoryChange={setCategoryFilter}
                            categories={categories}
                            isOnline={isOnline}
                            pendingCount={pendingCount}
                            isSyncing={isSyncing}
                            onOpenHistory={() => setShowHistoryModal(true)}
                            onAddManualItem={() => setShowCustomItemModal(true)}
                        />
                        <div className="flex-1 overflow-y-auto p-4 lg:p-6 pb-28 lg:pb-6 custom-scroll">
                            <ProductGrid
                                products={filteredProducts}
                                cart={cart}
                                onAddToCart={addToCart}
                            />
                        </div>
                    </div>
                )}

                {/* Debt mode: full width left panel (placeholder) */}
                {mode === "debt" && (
                    <div className={`flex-1 flex-col min-w-0 bg-gray-50 ${showMobileCart ? 'hidden lg:flex' : 'flex'}`}>
                        <div className="px-8 py-5 bg-white border-b border-gray-200">
                            <h2 className="text-lg font-bold text-gray-700">
                                Pembayaran Kasbon
                            </h2>
                            <p className="text-sm text-gray-400">
                                Pilih customer dan masukkan nominal pembayaran
                                hutang
                            </p>
                        </div>
                        <div className="flex-1 overflow-y-auto p-4 lg:p-8 custom-scroll">
                            <div className="max-w-xl mx-auto">
                                <div className="bg-amber-50 border border-amber-200 rounded-2xl p-5 mb-6">
                                    <div className="flex items-start gap-3">
                                        <svg
                                            className="w-5 h-5 text-amber-500 mt-0.5 shrink-0"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={2}
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                            />
                                        </svg>
                                        <div className="text-sm">
                                            <p className="font-bold text-amber-800 mb-1">
                                                Cara Kerja Bayar Kasbon
                                            </p>
                                            <ul className="text-amber-700 space-y-0.5 list-disc list-inside text-xs">
                                                <li>
                                                    Pilih customer dari panel
                                                    kanan
                                                </li>
                                                <li>
                                                    Sistem menampilkan semua
                                                    hutang yang belum lunas
                                                </li>
                                                <li>
                                                    Pembayaran dialokasikan
                                                    otomatis (hutang terlama
                                                    dulu)
                                                </li>
                                                <li>
                                                    Bisa bayar sebagian atau
                                                    lunas semua
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}

                {/* RIGHT: Panel (Cart atau Debt) */}
                {/* Backdrop for mobile */}
                {showMobileCart && (
                    <div 
                        className="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden transition-opacity"
                        onClick={() => setShowMobileCart(false)}
                    />
                )}
                
                <div className={`fixed inset-y-0 right-0 z-50 w-full sm:w-96 lg:relative lg:w-105 bg-white lg:border-l border-gray-200 flex-col shrink-0 shadow-2xl lg:shadow-lg min-h-0 overflow-hidden transform transition-transform duration-300 ease-in-out lg:translate-x-0 ${showMobileCart ? 'translate-x-0 flex' : 'translate-x-full lg:flex hidden'}`}>
                    
                    {/* Mobile Cart Header & Close Button */}
                    <div className="lg:hidden flex items-center justify-between p-4 border-b border-gray-100 bg-white">
                        <h2 className="text-lg font-bold text-gray-800">Keranjang Belanja</h2>
                        <button 
                            onClick={() => setShowMobileCart(false)}
                            className="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-full transition-colors"
                        >
                            <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {/* Tab switcher */}
                    <div className="flex border-b border-gray-200 shrink-0">
                        <button
                            onClick={() => setMode("pos")}
                            className={`flex-1 py-3.5 text-sm font-bold transition-all flex items-center justify-center gap-2 ${
                                mode === "pos"
                                    ? "bg-blue-600 text-white"
                                    : "bg-white text-gray-500 hover:bg-gray-50"
                            }`}
                        >
                            <svg
                                className="w-4 h-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"
                                />
                            </svg>
                            Transaksi
                        </button>
                        <button
                            onClick={() => setMode("debt")}
                            className={`flex-1 py-3.5 text-sm font-bold transition-all flex items-center justify-center gap-2 ${
                                mode === "debt"
                                    ? "bg-orange-500 text-white"
                                    : "bg-white text-gray-500 hover:bg-gray-50"
                            }`}
                        >
                            <svg
                                className="w-4 h-4"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"
                                />
                            </svg>
                            Bayar Kasbon
                        </button>
                    </div>

                    {/* Panel konten */}
                    {mode === "pos" ? (
                        <CartPanel
                            cart={cart}
                            totalItems={totalItems}
                            totalPrice={totalPrice}
                            paid={paid}
                            paidAmount={paidAmount}
                            change={change}
                            processing={processing}
                            errors={errors}
                            flash={flash}
                            showSuccess={showSuccess}
                            paidRef={paidRef}
                            activeShift={activeShift}
                            customers={customers}
                            selectedCustomerId={selectedCustomerId}
                            onUpdateQty={updateQty}
                            onRemove={removeFromCart}
                            onClear={clearCart}
                            onPaidChange={setPaid}
                            onCustomerChange={setSelectedCustomerId}
                            onCheckout={handleCheckout}
                            onCreateCustomer={handleCreateCustomer}
                        />
                    ) : (
                        <DebtPaymentPanel
                            customerDebts={customerDebts}
                            flash={flash}
                            errors={errors}
                        />
                    )}
                </div>

                {/* Mobile Floating Cart Button */}
                {!showMobileCart && cart.length > 0 && (
                    <div className="fixed bottom-6 inset-x-0 mx-4 lg:hidden z-30">
                        <button
                            onClick={() => setShowMobileCart(true)}
                            className="w-full bg-gradient-to-r from-blue-600 to-indigo-600 text-white p-4 rounded-2xl shadow-xl flex items-center justify-between font-bold active:scale-[0.98] transition-transform"
                        >
                            <div className="flex items-center gap-3">
                                <div className="bg-white/20 px-3 py-1.5 rounded-lg flex items-center gap-2 text-sm backdrop-blur-sm">
                                    <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                                    </svg>
                                    {totalItems} item
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                <span className="text-lg drop-shadow-sm">{formatRupiah(totalPrice)}</span>
                                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </button>
                    </div>
                )}
            </div>

            {/* Custom Item Modal */}
            {showCustomItemModal && (
                <div className="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/50 backdrop-blur-sm p-4 print:hidden">
                    <div className="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in-95 duration-200">
                        <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                            <h3 className="text-lg font-bold text-gray-800">
                                Tambah Item Manual
                            </h3>
                            <button
                                onClick={() => setShowCustomItemModal(false)}
                                className="text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div className="p-6 space-y-4">
                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-1">
                                    Nama Item <span className="text-red-500">*</span>
                                </label>
                                <input
                                    ref={customNameRef}
                                    type="text"
                                    className="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                                    placeholder="Contoh: Tambahan Daging"
                                    value={customItemName}
                                    onChange={(e) => setCustomItemName(e.target.value)}
                                    autoFocus
                                    onKeyDown={(e) => e.key === "Enter" && addCustomItem()}
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-gray-700 mb-1">
                                    Harga (Rp)
                                </label>
                                <input
                                    type="number"
                                    min="0"
                                    className="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                                    placeholder="Contoh: 15000"
                                    value={customItemPrice}
                                    onChange={(e) => setCustomItemPrice(e.target.value)}
                                    onKeyDown={(e) => e.key === "Enter" && addCustomItem()}
                                />
                            </div>
                        </div>
                        <div className="px-6 py-4 bg-gray-50 flex justify-end gap-3 rounded-b-2xl">
                            <button
                                onClick={() => setShowCustomItemModal(false)}
                                className="px-4 py-2 text-sm font-bold text-gray-600 hover:bg-gray-200 bg-gray-100 rounded-xl transition-colors"
                            >
                                Batal
                            </button>
                            <button
                                onClick={addCustomItem}
                                className="px-4 py-2 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors"
                            >
                                Tambahkan
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}
