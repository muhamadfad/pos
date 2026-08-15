import React from "react";
import { Link } from "@inertiajs/react";
import type { Category } from "./types";
import { usePwaInstall } from "../../hooks/usePwaInstall";
import { useWebBluetoothPrinter } from "../../hooks/useWebBluetoothPrinter";

interface HeaderProps {
    search: string;
    onSearchChange: (value: string) => void;
    onSearchKeyDown: (e: React.KeyboardEvent<HTMLInputElement>) => void;
    searchRef: React.RefObject<HTMLInputElement | null>;
    categoryFilter: number | null;
    onCategoryChange: (value: number | null) => void;
    categories: Category[];
    isOnline?: boolean;
    pendingCount?: number;
    isSyncing?: boolean;
    onOpenExpense?: () => void;
}

export default function Header({
    search,
    onSearchChange,
    onSearchKeyDown,
    searchRef,
    categoryFilter,
    onCategoryChange,
    categories,
    isOnline = true,
    pendingCount = 0,
    isSyncing = false,
    onOpenExpense,
}: HeaderProps) {
    const { isInstallable, installPwa } = usePwaInstall();
    const { isSupported, isConnected, isConnecting, printerName, connect, disconnect } = useWebBluetoothPrinter();

    return (
        <div className="bg-white/80 backdrop-blur-xl border-b border-gray-100 px-4 md:px-6 py-4 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 md:gap-6 shrink-0 sticky top-0 z-40 shadow-sm">
            <div className="flex items-center gap-4 w-full md:w-auto">
                <a
                    href="/dashboard"
                    className="flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-500 hover:text-gray-900 border border-gray-200 transition-all hover:shadow-sm"
                    title="Kembali ke Admin"
                >
                    <svg
                        className="w-5 h-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                        />
                    </svg>
                </a>

                {/* Tools/Actions Mobile First */}
                <div className="flex items-center gap-2 md:order-last">
                    {/* Install App Button */}
                    {isInstallable && (
                        <button
                            onClick={installPwa}
                            className="flex items-center justify-center gap-2 h-10 px-4 rounded-xl bg-blue-50 text-blue-600 font-bold text-xs sm:text-sm border border-blue-100 shadow-sm transition-all hover:bg-blue-100 hover:shadow-md hover:-translate-y-0.5 whitespace-nowrap active:scale-95"
                        >
                            <svg className="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span className="hidden sm:inline">Install App</span>
                        </button>
                    )}
                    
                    {/* Bluetooth Printer Web API Button */}
                    {(window.navigator as any).bluetooth && (
                        <button
                            onClick={connect}
                            disabled={isConnecting}
                            className={`flex items-center justify-center gap-2 h-10 px-4 rounded-xl font-bold text-xs sm:text-sm shadow-sm transition-all whitespace-nowrap active:scale-95 ${
                                isConnected 
                                    ? "bg-emerald-50 text-emerald-600 border border-emerald-200" 
                                    : "bg-white text-slate-600 border border-slate-200"
                            } ${isConnecting ? "opacity-75 cursor-not-allowed" : "hover:shadow-md hover:-translate-y-0.5"}`}
                            title={printerName || "Koneksi Printer Web Bluetooth"}
                        >
                            {isConnecting ? (
                                <svg className="w-4 h-4 sm:w-5 sm:h-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            ) : (
                                <svg className="w-4 h-4 sm:w-5 sm:h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2.5} d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                </svg>
                            )}
                            <span className="hidden sm:inline">
                                {isConnecting ? "Menyambung..." : isConnected ? "Printer Aktif" : "Printer"}
                            </span>
                        </button>
                    )}
                </div>

                <div className="flex items-center gap-3">
                    <div className="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg shadow-blue-500/20">
                        <svg
                            className="w-5 h-5 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z"
                            />
                        </svg>
                    </div>
                    <div>
                        <div className="flex items-center gap-2">
                            <h1 className="text-xl font-black text-gray-900 tracking-tight leading-none">AnoaPOS</h1>
                            <div className={`px-2 py-0.5 rounded-full text-[10px] font-bold flex items-center gap-1 ${
                                isOnline ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'
                            }`}>
                                <div className={`w-1.5 h-1.5 rounded-full ${isOnline ? 'bg-emerald-500' : 'bg-red-500 animate-pulse'}`}></div>
                                {isOnline ? 'Online' : 'Offline'}
                            </div>
                        </div>
                        <div className="flex items-center gap-2 mt-1">
                            <p className="text-xs font-semibold text-gray-400 uppercase tracking-wider">Point of Sale</p>
                            {pendingCount > 0 && (
                                <span className="text-[10px] font-bold bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded flex items-center gap-1">
                                    {isSyncing ? (
                                        <svg className="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                        </svg>
                                    ) : (
                                        <svg className="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                    )}
                                    {pendingCount} antrean
                                </span>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Right Side: Search and Filter */}
            <div className="w-full md:flex-1 max-w-2xl flex flex-col sm:flex-row items-center gap-3 md:gap-4">
                {/* Search */}
                <div className="flex-1 relative group">
                    <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg
                            className="w-5 h-5 text-gray-400 group-focus-within:text-blue-500 transition-colors"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                    </div>
                    <input
                        ref={searchRef}
                        type="text"
                        placeholder="Cari produk atau scan barcode..."
                        className="w-full pl-11 pr-4 py-2.5 text-sm font-medium bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all placeholder:text-gray-400 text-gray-900"
                        value={search}
                        onChange={(e) => onSearchChange(e.target.value)}
                        onKeyDown={onSearchKeyDown}
                    />
                </div>

                {/* Category filter */}
                <div className="relative shrink-0 w-full sm:w-auto">
                    <select
                        className="appearance-none w-full sm:w-auto bg-gray-50 border border-gray-200 text-gray-700 font-semibold text-sm rounded-xl pl-4 pr-10 py-2.5 outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 hover:bg-gray-100 transition-all cursor-pointer"
                        value={categoryFilter ?? ""}
                        onChange={(e) =>
                            onCategoryChange(
                                e.target.value ? Number(e.target.value) : null,
                            )
                        }
                    >
                        <option value="">Semua Kategori</option>
                        {categories.map((c) => (
                            <option key={c.id} value={c.id}>
                                {c.name}
                            </option>
                        ))}
                    </select>
                    <div className="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <svg className="w-4 h-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    );
}
