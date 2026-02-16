import React, { useState, useRef, useEffect, useMemo } from "react";
import { router, usePage } from "@inertiajs/react";
import type { PageProps, Product, CartItem } from "./types";
import Header from "./Header";
import ProductGrid from "./ProductGrid";
import CartPanel from "./CartPanel";

export default function PointOfSalePage() {
    const { products, categories, activeShift, flash, errors } =
        usePage<PageProps>().props;

    const [cart, setCart] = useState<CartItem[]>([]);
    const [search, setSearch] = useState("");
    const [categoryFilter, setCategoryFilter] = useState<number | null>(null);
    const [paid, setPaid] = useState<string>("");
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
            // Clear cart after successful transaction
            setCart([]);
            setPaid("");
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

    function handleCheckout() {
        if (cart.length === 0) return;
        if (paidAmount < totalPrice) {
            paidRef.current?.focus();
            return;
        }

        setProcessing(true);
        router.post(
            "/pos/checkout",
            {
                items: cart.map((c) => ({
                    product_id: c.product_id,
                    qty: c.qty,
                    price: c.price,
                })),
                paid: paidAmount,
            },
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Clear cart on successful checkout
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
                {/* LEFT: Product Grid */}
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

                {/* RIGHT: Cart Panel */}
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
                    onUpdateQty={updateQty}
                    onRemove={removeFromCart}
                    onClear={clearCart}
                    onPaidChange={setPaid}
                    onCheckout={handleCheckout}
                />
            </div>
        </>
    );
}
