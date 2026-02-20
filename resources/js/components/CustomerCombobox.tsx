import React, { useState, useRef, useEffect, useMemo } from "react";

export interface CustomerOption {
    id: number;
    name: string;
    type: string | null;
    phone: string | null;
}

interface CustomerComboboxProps {
    customers: CustomerOption[];
    value: number | null;
    onChange: (customerId: number | null) => void;
    label?: string;
    placeholder?: string;
    allowWalkIn?: boolean;
    /** Pass a name to render a hidden <input> for form submission */
    inputName?: string;
    className?: string;
}

const TYPE_LABEL: Record<string, string> = {
    store: "Toko",
    project: "Proyek",
    retail: "Eceran",
};

const TYPE_COLOR: Record<string, string> = {
    store: "bg-emerald-50 text-emerald-600 border-emerald-100",
    project: "bg-blue-50 text-blue-600 border-blue-100",
    retail: "bg-slate-50 text-slate-600 border-slate-100",
};

function typeLabel(type: string | null) {
    return TYPE_LABEL[type ?? ""] ?? "Umum";
}

function typeColor(type: string | null) {
    return TYPE_COLOR[type ?? ""] ?? "bg-gray-50 text-gray-500 border-gray-100";
}

export default function CustomerCombobox({
    customers,
    value,
    onChange,
    label = "Customer",
    placeholder = "Cari nama atau nomor HP...",
    allowWalkIn = true,
    inputName,
    className = "",
}: CustomerComboboxProps) {
    const [open, setOpen] = useState(false);
    const [search, setSearch] = useState("");
    const inputRef = useRef<HTMLInputElement>(null);
    const containerRef = useRef<HTMLDivElement>(null);

    const safeCustomers = customers ?? [];

    const selected = safeCustomers.find((c) => c.id === value) ?? null;

    const filtered = useMemo(() => {
        const q = search.toLowerCase().trim();
        const list = q
            ? safeCustomers.filter(
                  (c) =>
                      c.name.toLowerCase().includes(q) ||
                      (c.phone ?? "").includes(q),
              )
            : safeCustomers;
        return list.slice(0, 10);
    }, [safeCustomers, search]);

    // Close on outside click
    useEffect(() => {
        function handler(e: MouseEvent) {
            if (
                containerRef.current &&
                !containerRef.current.contains(e.target as Node)
            ) {
                setOpen(false);
            }
        }
        document.addEventListener("mousedown", handler);
        return () => document.removeEventListener("mousedown", handler);
    }, []);

    function handleSelect(customer: CustomerOption | null) {
        onChange(customer?.id ?? null);
        setSearch("");
        setOpen(false);
    }

    function handleClear() {
        onChange(null);
        setSearch("");
        setTimeout(() => inputRef.current?.focus(), 50);
    }

    return (
        <div ref={containerRef} className={`relative ${className}`}>
            {/* Hidden input for form submission */}
            {inputName && (
                <input type="hidden" name={inputName} value={value ?? ""} />
            )}

            {/* Label */}
            {label && (
                <label className="block text-xs font-bold text-slate-600 uppercase tracking-wide mb-1.5">
                    {label}
                    {allowWalkIn && (
                        <span className="text-slate-400 font-normal normal-case tracking-normal ml-1.5">
                            (opsional)
                        </span>
                    )}
                </label>
            )}

            {/* Selected Badge */}
            {selected ? (
                <div className="flex items-center justify-between px-3 py-2.5 bg-indigo-50 border border-indigo-200 rounded-xl">
                    <div className="flex items-center gap-2.5">
                        <div className="w-8 h-8 rounded-full bg-indigo-600 flex items-center justify-center text-white text-xs font-bold shrink-0">
                            {selected.name.charAt(0).toUpperCase()}
                        </div>
                        <div>
                            <p className="text-sm font-semibold text-indigo-800 leading-tight">
                                {selected.name}
                            </p>
                            <div className="flex items-center gap-1.5 mt-0.5">
                                <span
                                    className={`text-[10px] font-bold px-1.5 py-0.5 rounded border uppercase tracking-wide ${typeColor(selected.type)}`}
                                >
                                    {typeLabel(selected.type)}
                                </span>
                                <span className="text-xs text-indigo-400">
                                    {selected.phone ?? "Tidak ada no. HP"}
                                </span>
                            </div>
                        </div>
                    </div>
                    <button
                        type="button"
                        onClick={handleClear}
                        className="text-indigo-300 hover:text-red-500 transition-colors p-1"
                        aria-label="Hapus pilihan"
                    >
                        <svg
                            className="w-4 h-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            ) : (
                /* Search Input */
                <div className="relative">
                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg
                            className="w-4 h-4"
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
                        ref={inputRef}
                        type="text"
                        value={search}
                        onChange={(e) => {
                            setSearch(e.target.value);
                            setOpen(true);
                        }}
                        onFocus={() => setOpen(true)}
                        onKeyDown={(e) => {
                            if (e.key === "Escape") {
                                setOpen(false);
                                setSearch("");
                            }
                        }}
                        placeholder={placeholder}
                        autoComplete="off"
                        className="w-full pl-9 pr-3 py-2.5 text-sm bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all"
                    />
                </div>
            )}

            {/* Hint */}
            {!selected && !open && allowWalkIn && (
                <p className="text-xs text-slate-400 mt-1">
                    Ketik untuk mencari, atau biarkan kosong untuk Walk-in
                    Customer
                </p>
            )}

            {/* Dropdown */}
            {open && (
                <div className="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg overflow-hidden max-h-56 overflow-y-auto">
                    {/* Walk-in option */}
                    {allowWalkIn && (
                        <button
                            type="button"
                            onClick={() => handleSelect(null)}
                            className="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-slate-50 border-b border-slate-100 transition-colors text-left"
                        >
                            <div className="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-slate-500 shrink-0">
                                <svg
                                    className="w-4 h-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <p className="text-sm font-semibold text-slate-700">
                                    Walk-in Customer
                                </p>
                                <p className="text-xs text-slate-400">
                                    Pelanggan umum tanpa data
                                </p>
                            </div>
                        </button>
                    )}

                    {/* Customer list */}
                    {filtered.length > 0 ? (
                        filtered.map((c) => (
                            <button
                                key={c.id}
                                type="button"
                                onClick={() => handleSelect(c)}
                                className="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-indigo-50 border-b last:border-0 border-slate-100 transition-colors text-left"
                            >
                                <div className="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-xs font-bold shrink-0">
                                    {c.name.charAt(0).toUpperCase()}
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-sm font-semibold text-slate-800 truncate">
                                        {c.name}
                                    </p>
                                    <div className="flex items-center gap-1.5 mt-0.5">
                                        <span
                                            className={`text-[10px] font-bold px-1.5 py-0.5 rounded border uppercase tracking-wide ${typeColor(c.type)}`}
                                        >
                                            {typeLabel(c.type)}
                                        </span>
                                        <span className="text-xs text-slate-400">
                                            {c.phone ?? "-"}
                                        </span>
                                    </div>
                                </div>
                            </button>
                        ))
                    ) : (
                        <div className="px-4 py-3 text-sm text-slate-400 text-center">
                            Tidak ditemukan —{" "}
                            <span className="font-medium">"{search}"</span>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}
