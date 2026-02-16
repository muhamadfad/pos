import React from "react";
import type { Category } from "./types";

interface HeaderProps {
    search: string;
    onSearchChange: (value: string) => void;
    onSearchKeyDown: (e: React.KeyboardEvent<HTMLInputElement>) => void;
    searchRef: React.RefObject<HTMLInputElement | null>;
    categoryFilter: number | null;
    onCategoryChange: (value: number | null) => void;
    categories: Category[];
}

export default function Header({
    search,
    onSearchChange,
    onSearchKeyDown,
    searchRef,
    categoryFilter,
    onCategoryChange,
    categories,
}: HeaderProps) {
    return (
        <div className="bg-white border-b px-4 py-3 flex items-center gap-3 shrink-0">
            <div className="flex items-center gap-2">
                <div className="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
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
                <h1 className="text-lg font-bold text-gray-800">Kasir POS</h1>
            </div>

            {/* Search */}
            <div className="flex-1 max-w-md relative">
                <svg
                    className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
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
                <input
                    ref={searchRef}
                    type="text"
                    placeholder="Cari produk atau scan barcode..."
                    className="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
                    value={search}
                    onChange={(e) => onSearchChange(e.target.value)}
                    onKeyDown={onSearchKeyDown}
                />
            </div>

            {/* Category filter */}
            <select
                className="text-sm border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none"
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
        </div>
    );
}
