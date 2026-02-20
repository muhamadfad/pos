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

export interface Customer {
    id: number;
    name: string;
    type: string;
    phone: string | null;
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

export interface DebtOrder {
    id: number;
    invoice_number: string | null;
    date: string;
    total_amount: number;
    total_paid: number;
    remaining_receivable: number;
    payment_status: "unpaid" | "partial";
}

export interface CustomerDebt {
    id: number;
    name: string;
    phone: string | null;
    total_debt: number;
    orders: DebtOrder[];
}

export interface PageProps {
    [key: string]: unknown;
    products: Product[];
    categories: Category[];
    customers: Customer[];
    customerDebts: CustomerDebt[];
    activeShift: CashierShift | null;
    flash?: {
        success?: string;
        debt_success?: string;
    };
    errors?: Record<string, string>;
}
