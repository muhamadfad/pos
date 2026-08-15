import React, { useEffect, useState } from "react";
import axios from "axios";
import type { TransactionDetails } from "./ReceiptPrint";

interface TransactionHistoryModalProps {
    isOpen: boolean;
    onClose: () => void;
    onReprint: (transaction: TransactionDetails) => void;
}

export default function TransactionHistoryModal({ isOpen, onClose, onReprint }: TransactionHistoryModalProps) {
    const [transactions, setTransactions] = useState<any[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (isOpen) {
            setLoading(true);
            axios.get("/pos/transactions")
                .then((res) => {
                    let data = res.data;
                    if (typeof data === 'string') {
                        // Jika terlempar halaman login (HTML string)
                        data = [];
                    } else if (data && typeof data === 'object' && !Array.isArray(data)) {
                        // Jika response json menjadi object keys e.g. {"0": {...}}
                        data = Object.values(data);
                    } else if (!Array.isArray(data)) {
                        data = [];
                    }
                    setTransactions(data);
                })
                .catch((err) => {
                    console.error("Gagal mengambil riwayat transaksi:", err);
                })
                .finally(() => {
                    setLoading(false);
                });
        }
    }, [isOpen]);

    if (!isOpen) return null;

    const formatRupiah = (num: number) =>
        new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(num);

    return (
        <div className="fixed inset-0 z-[60] bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div className="bg-white rounded-3xl w-full max-w-4xl shadow-2xl overflow-hidden flex flex-col max-h-[85vh]">
                {/* Header */}
                <div className="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <div>
                        <h2 className="text-xl font-bold text-slate-800">Riwayat Transaksi</h2>
                        <p className="text-sm text-slate-500">Menampilkan transaksi yang sudah tersinkronisasi di shift ini.</p>
                    </div>
                    <button
                        onClick={onClose}
                        className="w-10 h-10 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-200 hover:text-slate-600 transition-colors"
                    >
                        <svg className="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {/* Content */}
                <div className="p-6 overflow-y-auto flex-1">
                    {loading ? (
                        <div className="flex justify-center items-center py-10">
                            <div className="animate-spin rounded-full h-10 w-10 border-b-2 border-indigo-600"></div>
                        </div>
                    ) : transactions.length === 0 ? (
                        <div className="text-center py-10">
                            <p className="text-slate-500">Belum ada transaksi di shift ini yang tersinkronisasi ke server.</p>
                        </div>
                    ) : (
                        <div className="overflow-x-auto">
                            <table className="w-full text-sm text-left">
                                <thead className="bg-slate-50 text-slate-600 uppercase text-[11px] font-bold tracking-widest border-b border-slate-200">
                                    <tr>
                                        <th className="px-6 py-4">Waktu</th>
                                        <th className="px-6 py-4">Invoice</th>
                                        <th className="px-6 py-4">Pelanggan</th>
                                        <th className="px-6 py-4 text-right">Total</th>
                                        <th className="px-6 py-4 text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 border-b border-slate-100">
                                    {transactions.map((trx) => (
                                        <tr key={trx.id} className="hover:bg-slate-50/80 transition-colors">
                                            <td className="px-6 py-4 whitespace-nowrap text-slate-600 font-medium">
                                                {trx.date}
                                            </td>
                                            <td className="px-6 py-4 font-bold text-indigo-600">
                                                {trx.invoice_number}
                                            </td>
                                            <td className="px-6 py-4 text-slate-800">
                                                {trx.customerName}
                                            </td>
                                            <td className="px-6 py-4 text-right font-bold text-slate-900">
                                                {formatRupiah(trx.totalPrice)}
                                            </td>
                                            <td className="px-6 py-4 text-center">
                                                <button
                                                    onClick={() => onReprint(trx)}
                                                    className="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 hover:text-indigo-800 rounded-lg text-xs font-bold transition-colors"
                                                >
                                                    <svg className="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    Cetak Ulang
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
