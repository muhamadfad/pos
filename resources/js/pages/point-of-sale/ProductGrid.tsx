import React from "react";
import type { Product, CartItem } from "./types";
import ProductCard from "./ProductCard";

interface ProductGridProps {
    products: Product[];
    cart: CartItem[];
    onAddToCart: (product: Product) => void;
}

export default function ProductGrid({
    products,
    cart,
    onAddToCart,
}: ProductGridProps) {
    if (products.length === 0) {
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
        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3">
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
