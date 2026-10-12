export interface Product {
    id: number;
    barcode: string;
    name: string;
    price: number;
    stock: number;
    category: string;
    category_id: number;
    image?: string | null;
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
    barcode?: string | null;
    name: string;
    price: number;
    qty: number;
    stock?: number;
    image?: string | null;
    is_custom?: boolean;
    notes?: string;
}

export interface CashierShift {
    id: number;
    opened_at: string;
    opening_balance: number;
    total_sales: number;
    transaction_count: number;
    total_transfer?: number;
    total_expense?: number;
    total_cash_income?: number;
    cashier_name?: string;
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
    authUser: {
        id: number;
        name: string;
        branch_id: number;
    };
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
