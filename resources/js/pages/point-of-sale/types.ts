export interface Product {
    id: number;
    barcode: string;
    name: string;
    price: number;
    stock: number;
    category: string;
    category_id: number;
}

export interface Category {
    id: number;
    name: string;
}

export interface CartItem {
    product_id: number;
    barcode: string;
    name: string;
    price: number;
    qty: number;
    stock: number;
}

export interface CashierShift {
    id: number;
    opened_at: string;
    opening_balance: number;
    total_sales: number;
    transaction_count: number;
}

export interface PageProps {
    products: Product[];
    categories: Category[];
    activeShift: CashierShift | null;
    flash?: {
        success?: string;
    };
    errors?: Record<string, string>;
}
