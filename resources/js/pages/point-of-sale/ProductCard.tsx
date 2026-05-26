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
            className={`relative group rounded-2xl p-5 text-left transition-all duration-300 transform active:scale-95 flex flex-col justify-between h-full min-h-[200px]
                ${
                    qtyInCart > 0
                        ? "bg-white border-2 border-blue-500 shadow-lg shadow-blue-500/20"
                        : "bg-white border border-gray-100 shadow-sm hover:shadow-xl hover:border-blue-300 hover:-translate-y-1"
                }`}
        >
            {qtyInCart > 0 && (
                <div className="absolute -top-3 -right-3 z-10 w-8 h-8 bg-gradient-to-br from-blue-500 to-indigo-600 text-white text-sm font-black rounded-full flex items-center justify-center shadow-md shadow-blue-500/30 transform scale-100 transition-transform">
                    <span className="animate-pulse">{qtyInCart}</span>
                </div>
            )}
            
            <div className="w-full h-36 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl flex items-center justify-center mb-4 group-hover:from-blue-50 group-hover:to-indigo-50 transition-colors overflow-hidden relative">
                {product.image ? (
                    <img src={product.image} alt={product.name} className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                ) : (
                    <svg
                        className={`w-10 h-10 transition-colors ${qtyInCart > 0 ? "text-blue-500" : "text-gray-300 group-hover:text-blue-400"}`}
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
                )}
            </div>
            
            <div className="flex-1 flex flex-col justify-end w-full">
                <p className="text-[10px] text-gray-400 truncate tracking-wider uppercase font-semibold mb-0.5">{product.barcode || 'NO-BARCODE'}</p>
                <p className={`text-sm font-bold truncate leading-tight mb-1 transition-colors ${qtyInCart > 0 ? "text-blue-700" : "text-gray-800 group-hover:text-gray-900"}`}>
                    {product.name}
                </p>
                
                <div className="flex items-end justify-between mt-auto pt-2 border-t border-gray-50">
                    <div>
                        <p className="text-[10px] text-gray-400 mb-0.5">{product.category}</p>
                        <p className={`text-base font-black transition-colors ${qtyInCart > 0 ? "text-blue-600" : "text-gray-900"}`}>
                            {formatRupiah(product.price)}
                        </p>
                    </div>
                    <div className="flex flex-col items-end">
                        <span className="text-[10px] font-medium text-gray-400 mb-0.5">Stok</span>
                        <span
                            className={`text-xs font-bold px-2 py-0.5 rounded-md shadow-sm ${
                                product.stock <= 5
                                    ? "bg-red-50 text-red-600 border border-red-100"
                                    : "bg-emerald-50 text-emerald-600 border border-emerald-100"
                            }`}
                        >
                            {product.stock}
                        </span>
                    </div>
                </div>
            </div>
        </button>
    );
}
