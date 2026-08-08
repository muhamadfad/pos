import React, { useState } from "react";
import { router } from "@inertiajs/react";

const formatRupiah = (number: number) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(number);
};

interface ExpenseModalProps {
    isOpen: boolean;
    onClose: () => void;
}

export default function ExpenseModal({ isOpen, onClose }: ExpenseModalProps) {
    const [amount, setAmount] = useState("");
    const [description, setDescription] = useState("");
    const [category, setCategory] = useState("Operasional");
    const [isSubmitting, setIsSubmitting] = useState(false);

    if (!isOpen) return null;

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        
        const numAmount = parseInt(amount.replace(/[^0-9]/g, ""), 10);
        if (!numAmount || numAmount <= 0) {
            alert("Nominal tidak valid");
            return;
        }

        setIsSubmitting(true);
        router.post(route('kasir.expenses.store'), {
            amount: numAmount,
            description,
            category
        }, {
            onSuccess: () => {
                setAmount("");
                setDescription("");
                setCategory("Operasional");
                setIsSubmitting(false);
                onClose();
            },
            onError: (errors) => {
                console.error(errors);
                alert("Gagal menyimpan pengeluaran. Periksa input Anda.");
                setIsSubmitting(false);
            }
        });
    };

    const handleAmountChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const val = e.target.value.replace(/[^0-9]/g, "");
        if (val) {
            setAmount(formatRupiah(parseInt(val, 10)));
        } else {
            setAmount("");
        }
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div className="bg-slate-900 border border-slate-700 rounded-3xl w-full max-w-md overflow-hidden shadow-2xl flex flex-col">
                <div className="p-6 border-b border-slate-800 flex justify-between items-center bg-slate-800/50">
                    <h2 className="text-xl font-bold text-white flex items-center gap-2">
                        <span className="iconify text-rose-500" data-icon="mdi:cash-minus"></span>
                        Catat Pengeluaran
                    </h2>
                    <button 
                        onClick={onClose}
                        className="p-2 hover:bg-slate-700 rounded-xl transition text-slate-400 hover:text-white"
                        disabled={isSubmitting}
                    >
                        <span className="iconify text-xl" data-icon="mdi:close"></span>
                    </button>
                </div>
                
                <form onSubmit={handleSubmit} className="p-6 flex-1 overflow-y-auto space-y-5">
                    <div>
                        <label className="block text-sm font-medium text-slate-300 mb-2">Nominal (Rp)</label>
                        <input
                            type="text"
                            value={amount}
                            onChange={handleAmountChange}
                            placeholder="Contoh: 50.000"
                            className="w-full bg-slate-800 border-slate-700 text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500 font-mono text-lg"
                            required
                        />
                    </div>
                    
                    <div>
                        <label className="block text-sm font-medium text-slate-300 mb-2">Kategori</label>
                        <select
                            value={category}
                            onChange={(e) => setCategory(e.target.value)}
                            className="w-full bg-slate-800 border-slate-700 text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                        >
                            <option value="Operasional">Operasional</option>
                            <option value="Bahan Baku">Bahan Baku (Darurat)</option>
                            <option value="Listrik/Air">Listrik / Air</option>
                            <option value="Gaji/Bonus">Gaji / Bonus</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div>
                        <label className="block text-sm font-medium text-slate-300 mb-2">Keterangan</label>
                        <textarea
                            value={description}
                            onChange={(e) => setDescription(e.target.value)}
                            placeholder="Contoh: Beli es batu, Beli token listrik..."
                            className="w-full bg-slate-800 border-slate-700 text-white rounded-xl focus:ring-emerald-500 focus:border-emerald-500"
                            rows={3}
                            required
                        ></textarea>
                    </div>

                    <button
                        type="submit"
                        disabled={isSubmitting || !amount || !description}
                        className="w-full py-3.5 bg-emerald-500 hover:bg-emerald-600 disabled:opacity-50 disabled:cursor-not-allowed text-white font-bold rounded-xl flex items-center justify-center gap-2 transition"
                    >
                        {isSubmitting ? (
                            <span className="iconify animate-spin text-xl" data-icon="mdi:loading"></span>
                        ) : (
                            <span className="iconify text-xl" data-icon="mdi:content-save"></span>
                        )}
                        Simpan Pengeluaran
                    </button>
                </form>
            </div>
        </div>
    );
}
