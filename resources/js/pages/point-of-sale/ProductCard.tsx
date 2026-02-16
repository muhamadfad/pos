import React from "react";
import type { Product } from "./types";
import { formatRupiah } from "./utils";

interface ProductCardProps {
    product: Product;
    qtyInCart: number;
    onAdd: () => void;
}

export default function ProductCard({
    product,
    qtyInCart,
    onAdd,
}: ProductCardProps) {
    return (
        <button
            onClick={onAdd}
            className={`relative bg-white rounded-xl border-2 p-3 text-left transition-all hover:shadow-md active:scale-[0.97] ${
                qtyInCart > 0
                    ? "border-blue-500 ring-1 ring-blue-200"
                    : "border-gray-200 hover:border-blue-300"
            }`}
        >
            {qtyInCart > 0 && (
                <span className="absolute -top-2 -right-2 w-6 h-6 bg-blue-600 text-white text-xs font-bold rounded-full flex items-center justify-center">
                    {qtyInCart}
                </span>
            )}
            <div className="w-full h-16 bg-gray-100 rounded-lg flex items-center justify-center mb-2">
                <svg
                    className="w-8 h-8 text-gray-300"
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
            </div>
            <p className="text-xs text-gray-400 truncate">{product.barcode}</p>
            <p className="text-sm font-medium text-gray-800 truncate leading-tight">
                {product.name}
            </p>
            <p className="text-xs text-gray-400 mt-0.5">{product.category}</p>
            <div className="flex items-center justify-between mt-2">
                <p className="text-sm font-bold text-blue-600">
                    {formatRupiah(product.price)}
                </p>
                <span
                    className={`text-xs px-1.5 py-0.5 rounded ${
                        product.stock <= 5
                            ? "bg-red-100 text-red-700"
                            : "bg-green-100 text-green-700"
                    }`}
                >
                    {product.stock}
                </span>
            </div>
        </button>
    );
}
