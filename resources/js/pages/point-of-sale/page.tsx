import React, { useState, useRef, useEffect, useMemo } from "react";
import { router, usePage } from "@inertiajs/react";
import type { PageProps, Product, CartItem } from "./types";
import Header from "./Header";
import ProductGrid from "./ProductGrid";
import CartPanel from "./CartPanel";
import DebtPaymentPanel from "./DebtPaymentPanel";

export default function PointOfSalePage() {
    const {
        products,
        categories,
        customers,
        customerDebts,
        activeShift,
        flash,
        errors,
    } = usePage<PageProps>().props;

    // Mode: "pos" = transaksi baru, "debt" = bayar kasbon
    const [mode, setMode] = useState<"pos" | "debt">("pos");

    const [cart, setCart] = useState<CartItem[]>([]);
    const [search, setSearch] = useState("");
    const [categoryFilter, setCategoryFilter] = useState<number | null>(null);
    const [paid, setPaid] = useState<string>("");
    const [selectedCustomerId, setSelectedCustomerId] = useState<number | null>(
        null,
    );
    const [showSuccess, setShowSuccess] = useState(false);
    const [processing, setProcessing] = useState(false);

    const searchRef = useRef<HTMLInputElement>(null);
    const paidRef = useRef<HTMLInputElement>(null);

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
                p.barcode.toLowerCase().includes(search.toLowerCase());
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
                if (existing.qty >= product.stock) return prev;
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
                    stock: product.stock,
                },
            ];
        });
    }

    function updateQty(productId: number, qty: number) {
        if (qty < 1) {
            removeFromCart(productId);
            return;
        }
        setCart((prev) =>
            prev.map((c) =>
                c.product_id === productId
                    ? { ...c, qty: Math.min(qty, c.stock) }
                    : c,
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
                (p) => p.barcode.toLowerCase() === search.trim().toLowerCase(),
            );
            if (product) {
                addToCart(product);
                setSearch("");
            }
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

        setProcessing(true);
        router.post(
            "/pos/checkout",
            {
                items: cart.map((c) => ({
                    product_id: c.product_id,
                    qty: c.qty,
                    price: c.price,
                })),
                paid: actualPaid,
                customer_id: selectedCustomerId ?? undefined,
                payment_method: paymentMethod,
                payment_type: paymentType,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    setCart([]);
                    setPaid("");
                    searchRef.current?.focus();
                },
                onFinish: () => setProcessing(false),
            },
        );
    }

    const formatRupiah = (num: number) =>
        new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(num);

    return (
        <>
            <div className="flex h-screen bg-gray-100 overflow-hidden">
                {/* LEFT: Product Grid — hanya tampil saat mode pos */}
                {mode === "pos" && (
                    <div className="flex-1 flex flex-col min-w-0">
                        <Header
                            search={search}
                            onSearchChange={setSearch}
                            onSearchKeyDown={handleSearchKeyDown}
                            searchRef={searchRef}
                            categoryFilter={categoryFilter}
                            onCategoryChange={setCategoryFilter}
                            categories={categories}
                        />
                        <div className="flex-1 overflow-y-auto p-4 custom-scroll">
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
                    <div className="flex-1 flex flex-col min-w-0 bg-gray-50">
                        <div className="px-8 py-5 bg-white border-b border-gray-200">
                            <h2 className="text-lg font-bold text-gray-700">
                                Pembayaran Kasbon
                            </h2>
                            <p className="text-sm text-gray-400">
                                Pilih customer dan masukkan nominal pembayaran
                                hutang
                            </p>
                        </div>
                        <div className="flex-1 overflow-y-auto p-8 custom-scroll">
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
                <div className="w-105 bg-white border-l border-gray-200 flex flex-col shrink-0 shadow-lg min-h-0 overflow-hidden">
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
                        />
                    ) : (
                        <DebtPaymentPanel
                            customerDebts={customerDebts}
                            flash={flash}
                            errors={errors}
                        />
                    )}
                </div>
            </div>
        </>
    );
}
