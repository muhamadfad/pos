import Dexie, { type Table } from 'dexie';

export interface SyncQueueItem {
    id?: number;
    client_uuid: string;
    type: 'order' | 'customer';
    status: 'pending' | 'failed' | 'success';
    retry_count: number;
    payload: any;
}

export interface LocalCustomer {
    id: number;
    name: string;
    phone: string | null;
    is_synced: boolean;
}

export class POSDatabase extends Dexie {
    sync_queue!: Table<SyncQueueItem, number>;
    customers!: Table<LocalCustomer, number>;
    
    constructor() {
        super('POSDatabase');
        this.version(2).stores({
            sync_queue: '++id, client_uuid, type, status, retry_count',
            customers: 'id, name, is_synced' // index id as negative values for offline customers
        });
    }
}

export const db = new POSDatabase();
