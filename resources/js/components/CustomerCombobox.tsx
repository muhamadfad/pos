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
    inputName?: string;
    className?: string;
    onCreateCustomer?: (name: string) => void;
}

const TYPE_LABEL: Record<string, string> = {
    store: "Toko",
    project: "Proyek",
    retail: "Eceran",
};

const TYPE_COLOR: Record<string, string> = {
    store: "bg-emerald-50 text-emerald-600 border-emerald-100",
    project: "bg-blue-50 text-blue-600 border-blue-100",
    retail: "bg-[#f5f5f5] text-[rgba(0,0,0,0.65)] border-[#f0f0f0]",
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
    onCreateCustomer,
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
                <label className="block text-xs font-bold text-[rgba(0,0,0,0.65)] uppercase tracking-wide mb-1.5">
                    {label}
                    {allowWalkIn && (
                        <span className="text-white/65 font-normal normal-case tracking-normal ml-1.5">
                            (opsional)
                        </span>
                    )}
                </label>
            )}

            {/* Selected Badge */}
            {selected ? (
                <div className="flex items-center justify-between px-3 py-2.5 bg-[#e6f4ff] border border-indigo-200 rounded-lg">
                    <div className="flex items-center gap-2.5">
                        <div className="w-8 h-8 rounded-full bg-[#1677ff] flex items-center justify-center text-white text-xs font-bold shrink-0">
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
                                <span className="text-xs text-[#1677ff]">
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
                    <div className="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-white/65">
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
                        className="w-full pl-9 pr-3 py-2.5 text-sm bg-white border border-[#f0f0f0] rounded-lg focus:outline-none focus:ring-4 focus:ring-[#1677ff]/10 focus:border-indigo-500 transition-all"
                    />
                </div>
            )}

            {/* Hint */}
            {!selected && !open && allowWalkIn && (
                <p className="text-xs text-white/65 mt-1">
                    Ketik untuk mencari, atau biarkan kosong untuk Walk-in
                    Customer
                </p>
            )}

            {/* Dropdown */}
            {open && (
                <div className="absolute z-50 w-full mt-1 bg-white border border-[#f0f0f0] rounded-lg shadow-[0_6px_16px_0_rgba(0,0,0,0.08),0_3px_6px_-4px_rgba(0,0,0,0.12),0_9px_28px_8px_rgba(0,0,0,0.05)] overflow-hidden max-h-56 overflow-y-auto">
                    {/* Walk-in option */}
                    {allowWalkIn && (
                        <button
                            type="button"
                            onClick={() => handleSelect(null)}
                            className="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-[#f5f5f5] border-b border-[#f0f0f0] transition-colors text-left"
                        >
                            <div className="w-7 h-7 rounded-full bg-slate-200 flex items-center justify-center text-[rgba(0,0,0,0.45)] shrink-0">
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
                                <p className="text-xs text-white/65">
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
                                className="w-full flex items-center gap-3 px-4 py-2.5 hover:bg-[#e6f4ff] border-b last:border-0 border-[#f0f0f0] transition-colors text-left"
                            >
                                <div className="w-7 h-7 rounded-full bg-[#e6f4ff] flex items-center justify-center text-[#1677ff] text-xs font-bold shrink-0">
                                    {c.name.charAt(0).toUpperCase()}
                                </div>
                                <div className="flex-1 min-w-0">
                                    <p className="text-sm font-semibold text-[rgba(0,0,0,0.88)] truncate">
                                        {c.name}
                                    </p>
                                    <div className="flex items-center gap-1.5 mt-0.5">
                                        <span
                                            className={`text-[10px] font-bold px-1.5 py-0.5 rounded border uppercase tracking-wide ${typeColor(c.type)}`}
                                        >
                                            {typeLabel(c.type)}
                                        </span>
                                        <span className="text-xs text-white/65">
                                            {c.phone ?? "-"}
                                        </span>
                                    </div>
                                </div>
                            </button>
                        ))
                    ) : (
                        <div className="px-4 py-3 text-sm text-white/65 text-center">
                            Tidak ditemukan —{" "}
                            <span className="font-medium">"{search}"</span>
                            {onCreateCustomer && search.trim() && (
                                <button
                                    type="button"
                                    onClick={() => {
                                        onCreateCustomer(search.trim());
                                        setSearch("");
                                        setOpen(false);
                                    }}
                                    className="mt-2 w-full py-2 bg-[#e6f4ff] hover:bg-[#e6f4ff] text-[#0958d9] font-semibold rounded-lg transition-colors"
                                >
                                    + Tambah "{search.trim()}"
                                </button>
                            )}
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}
