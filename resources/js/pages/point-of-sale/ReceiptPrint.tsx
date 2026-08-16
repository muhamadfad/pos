import React from "react";
import type { CartItem } from "./types";

export interface TransactionDetails {
    cart: CartItem[];
    totalPrice: number;
    paidAmount: number;
    change: number;
    paymentType: string;
    date: string;
    customerName: string;
    invoiceNumber?: string;
    cashierName?: string;
}

interface ReceiptPrintProps {
    transaction: TransactionDetails | null;
}

export default function ReceiptPrint({ transaction }: ReceiptPrintProps) {
    if (!transaction) return null;

    const formatRupiah = (num: number) =>
        new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(num);

    return (
        <div className="hidden print:block font-mono text-sm text-black bg-white p-4 w-[80mm] mx-auto absolute top-0 left-0">
            <style>
                {`
                @media print {
                    @page { margin: 0; }
                    body * { visibility: hidden; }
                    .print\\:block, .print\\:block * { visibility: visible; }
                    .print\\:block { position: absolute; left: 0; top: 0; width: 100%; }
                }
                `}
            </style>
            
            <div className="text-center mb-4">
                <h2 className="font-bold text-lg mb-1">Coto Makassar Selera Rakyat</h2>
                <p className="text-xs font-semibold mb-1">Hub: +62 822-9037-1299</p>
                <p className="text-xs">
                    Terima kasih atas kunjungan Anda
                </p>
            </div>

            <div className="border-b border-dashed border-gray-400 mb-3 pb-3 text-xs">
                <div className="flex justify-between">
                    <span>Tanggal:</span>
                    <span>{transaction.date}</span>
                </div>
                <div className="flex justify-between">
                    <span>Pelanggan:</span>
                    <span>{transaction.customerName}</span>
                </div>
                <div className="flex justify-between">
                    <span>Tipe Bayar:</span>
                    <span className="uppercase">{transaction.paymentType}</span>
                </div>
                {transaction.cashierName && (
                    <div className="flex justify-between">
                        <span>Kasir:</span>
                        <span>{transaction.cashierName}</span>
                    </div>
                )}
            </div>

            <div className="mb-3 border-b border-dashed border-gray-400 pb-3">
                {transaction.cart.map((item, index) => (
                    <div key={index} className="mb-2">
                        <div className="font-semibold">{item.name}</div>
                        <div className="flex justify-between text-xs">
                            <span>{item.qty} x {formatRupiah(item.price)}</span>
                            <span>{formatRupiah(item.qty * item.price)}</span>
                        </div>
                    </div>
                ))}
            </div>

            <div className="space-y-1 text-sm font-semibold border-b border-dashed border-gray-400 pb-3 mb-3">
                <div className="flex justify-between">
                    <span>Total:</span>
                    <span>{formatRupiah(transaction.totalPrice)}</span>
                </div>
                <div className="flex justify-between">
                    <span>Dibayar:</span>
                    <span>{formatRupiah(transaction.paidAmount)}</span>
                </div>
                {transaction.change >= 0 ? (
                    <div className="flex justify-between">
                        <span>Kembali:</span>
                        <span>{formatRupiah(transaction.change)}</span>
                    </div>
                ) : (
                    <div className="flex justify-between text-black">
                        <span>Kasbon/Sisa:</span>
                        <span>{formatRupiah(Math.abs(transaction.change))}</span>
                    </div>
                )}
            </div>

            <div className="text-center text-xs">
                <p>Barang yang sudah dibeli tidak dapat</p>
                <p>ditukar/dikembalikan.</p>
            </div>
        </div>
    );
}
