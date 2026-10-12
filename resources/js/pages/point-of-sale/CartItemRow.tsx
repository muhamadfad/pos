import React, { useState } from "react";
import type { CartItem } from "./types";
import { formatRupiah } from "./utils";

interface CartItemRowProps {
    item: CartItem;
    index: number;
    onUpdateQty: (productId: number, qty: number, notes?: string) => void;
    onRemove: (productId: number, notes?: string) => void;
    onUpdateNote: (productId: number, oldNote: string | undefined, newNote: string) => void;
}

export default function CartItemRow({
    item,
    index,
    onUpdateQty,
    onRemove,
    onUpdateNote,
}: CartItemRowProps) {
    const [isEditingNote, setIsEditingNote] = useState(false);
    const [noteInput, setNoteInput] = useState(item.notes || "");
    
    const handleUpdateQty = (newQty: number) => {
        if (newQty < 1) {
            onRemove(item.product_id, item.notes);
            return;
        }
        onUpdateQty(item.product_id, newQty, item.notes);
    };

    const handleSaveNote = () => {
        onUpdateNote(item.product_id, item.notes, noteInput);
        setIsEditingNote(false);
    };

    return (
        <div className="group px-4 py-3 hover:bg-blue-50/50 transition-colors">
            <div className="flex items-start gap-3">
                {/* Nomor urut */}
                <span className="w-5 h-5 rounded-full bg-gray-200 text-[10px] font-bold text-gray-500 flex items-center justify-center shrink-0 mt-0.5">
                    {index}
                </span>

                {/* Info produk */}
                <div className="flex-1 min-w-0">
                    <div className="flex items-start justify-between gap-2">
                        <div>
                            <p className="text-sm font-semibold text-gray-800 truncate leading-tight">
                                {item.name}
                            </p>
                            <p className="text-xs text-gray-400 mt-0.5">
                                {formatRupiah(item.price)} /pcs
                            </p>
                        </div>
                    </div>

                    {/* Note Input / Display */}
                    <div className="mt-2">
                        {isEditingNote ? (
                            <div className="flex gap-2 items-center">
                                <input
                                    type="text"
                                    value={noteInput}
                                    onChange={(e) => setNoteInput(e.target.value)}
                                    placeholder="Contoh: Less sugar, extra shot..."
                                    className="w-full text-xs px-2 py-1 border border-blue-200 rounded-md focus:ring-1 focus:ring-blue-500 outline-none bg-white"
                                    onKeyDown={(e) => e.key === 'Enter' && handleSaveNote()}
                                    autoFocus
                                />
                                <button onClick={handleSaveNote} className="text-xs font-semibold text-blue-600 hover:text-blue-700 bg-blue-50 px-2 py-1 rounded-md shrink-0">Simpan</button>
                            </div>
                        ) : (
                            <div className="flex items-start gap-1">
                                {item.notes && (
                                    <span className="text-[11px] font-medium text-amber-600 bg-amber-50 px-2 py-0.5 rounded-md break-all">
                                        Catatan: {item.notes}
                                    </span>
                                )}
                                <button 
                                    onClick={() => {
                                        setNoteInput(item.notes || "");
                                        setIsEditingNote(true);
                                    }}
                                    className={`text-[10px] font-bold text-blue-500 hover:text-blue-700 px-1.5 py-0.5 rounded transition-colors ${item.notes ? '' : 'opacity-0 group-hover:opacity-100 bg-blue-50'}`}
                                >
                                    {item.notes ? 'Edit' : '+ Tambah Catatan'}
                                </button>
                            </div>
                        )}
                    </div>

                    {/* Qty controls */}
                    <div className="flex items-center gap-1.5 mt-2.5">
                        <button
                            onClick={() => handleUpdateQty(item.qty - 1)}
                            className="w-7 h-7 rounded-lg bg-gray-100 hover:bg-red-100 hover:text-red-600 text-gray-500 flex items-center justify-center text-sm font-bold transition-colors"
                        >
                            −
                        </button>
                        <input
                            type="number"
                            value={item.qty}
                            onChange={(e) => handleUpdateQty(parseInt(e.target.value) || 1)}
                            className="w-12 h-7 text-center text-sm font-semibold border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                            min={1}
                        />
                        <button
                            onClick={() => handleUpdateQty(item.qty + 1)}
                            className="w-7 h-7 rounded-lg bg-gray-100 hover:bg-green-100 hover:text-green-600 text-gray-500 flex items-center justify-center text-sm font-bold transition-colors"
                        >
                            +
                        </button>
                    </div>
                </div>

                {/* Subtotal & hapus */}
                <div className="flex flex-col items-end shrink-0 gap-1">
                    <p className="text-sm font-bold text-gray-800">
                        {formatRupiah(item.price * item.qty)}
                    </p>
                    <button
                        onClick={() => onRemove(item.product_id, item.notes)}
                        className="opacity-0 group-hover:opacity-100 text-[11px] text-red-400 hover:text-red-600 font-medium transition-opacity flex items-center gap-0.5"
                    >
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    );
}
