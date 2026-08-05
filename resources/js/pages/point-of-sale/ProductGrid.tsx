import React from "react";
import type { Product, CartItem } from "./types";
import ProductCard from "./ProductCard";

interface ProductGridProps {
    products: Product[];
    cart: CartItem[];
    onAddToCart: (product: Product) => void;
    onAddManualItem?: () => void;
}

export default function ProductGrid({
    products,
    cart,
    onAddToCart,
    onAddManualItem,
}: ProductGridProps) {
    if (products.length === 0 && !onAddManualItem) {
        return (
            <div className="flex flex-col items-center justify-center h-full text-gray-400">
                <svg
                    className="w-16 h-16 mb-3"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth={1.5}
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"
                    />
                </svg>
                <p className="text-sm">Produk tidak ditemukan</p>
            </div>
        );
    }

    return (
        <div className="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
            {/* Custom Item Card */}
            {onAddManualItem && (
                <button
                    onClick={onAddManualItem}
                    className="flex flex-col items-center justify-center p-6 bg-white border-2 border-dashed border-orange-300 rounded-2xl hover:border-orange-500 hover:bg-orange-50 transition-all group"
                >
                    <div className="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg className="w-6 h-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <span className="text-orange-700 font-bold text-sm text-center">Tambah Manual / Add-on</span>
                    <span className="text-orange-500 text-xs mt-1 text-center">Custom Harga</span>
                </button>
            )}

            {products.map((product) => {
                const inCart = cart.find((c) => c.product_id === product.id);
                return (
                    <ProductCard
                        key={product.id}
                        product={product}
                        qtyInCart={inCart?.qty ?? 0}
                        onAdd={() => onAddToCart(product)}
                    />
                );
            })}
        </div>
    );
}
