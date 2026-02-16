import React from "react";
import type { CartItem } from "./types";
import { formatRupiah } from "./utils";

interface CartItemRowProps {
    item: CartItem;
    index: number;
    onUpdateQty: (productId: number, qty: number) => void;
    onRemove: (productId: number) => void;
}

export default function CartItemRow({
    item,
    index,
    onUpdateQty,
    onRemove,
}: CartItemRowProps) {
    return (
        <div className="group px-4 py-3 hover:bg-blue-50/50 transition-colors">
            <div className="flex items-start gap-3">
                {/* Nomor urut */}
                <span className="w-5 h-5 rounded-full bg-gray-200 text-[10px] font-bold text-gray-500 flex items-center justify-center shrink-0 mt-0.5">
                    {index}
                </span>

                {/* Info produk */}
                <div className="flex-1 min-w-0">
                    <p className="text-sm font-semibold text-gray-800 truncate leading-tight">
                        {item.name}
                    </p>
                    <p className="text-xs text-gray-400 mt-0.5">
                        {formatRupiah(item.price)} /pcs
                    </p>

                    {/* Qty controls */}
                    <div className="flex items-center gap-1.5 mt-2">
                        <button
                            onClick={() =>
                                onUpdateQty(item.product_id, item.qty - 1)
                            }
                            className="w-7 h-7 rounded-lg bg-gray-100 hover:bg-red-100 hover:text-red-600 text-gray-500 flex items-center justify-center text-sm font-bold transition-colors"
                        >
                            −
                        </button>
                        <input
                            type="number"
                            value={item.qty}
                            onChange={(e) =>
                                onUpdateQty(
                                    item.product_id,
                                    parseInt(e.target.value) || 0,
                                )
                            }
                            className="w-12 h-7 text-center text-sm font-semibold border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                            min={1}
                            max={item.stock}
                        />
                        <button
                            onClick={() =>
                                onUpdateQty(item.product_id, item.qty + 1)
                            }
                            disabled={item.qty >= item.stock}
                            className="w-7 h-7 rounded-lg bg-gray-100 hover:bg-green-100 hover:text-green-600 text-gray-500 disabled:opacity-40 disabled:cursor-not-allowed flex items-center justify-center text-sm font-bold transition-colors"
                        >
                            +
                        </button>
                        {item.qty >= item.stock && (
                            <span className="text-[10px] text-amber-600 font-medium ml-1">
                                Maks
                            </span>
                        )}
                    </div>
                </div>

                {/* Subtotal & hapus */}
                <div className="flex flex-col items-end shrink-0 gap-1">
                    <p className="text-sm font-bold text-gray-800">
                        {formatRupiah(item.price * item.qty)}
                    </p>
                    <button
                        onClick={() => onRemove(item.product_id)}
                        className="opacity-0 group-hover:opacity-100 text-[11px] text-red-400 hover:text-red-600 font-medium transition-opacity flex items-center gap-0.5"
                    >
                        <svg
                            className="w-3 h-3"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    );
}
