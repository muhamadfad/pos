import { useEffect, useState } from 'react';
import axios from 'axios';
import { db } from '../lib/db';
import { useLiveQuery } from 'dexie-react-hooks';

export function usePosSync() {
    const [isOnline, setIsOnline] = useState(navigator.onLine);
    const [isSyncing, setIsSyncing] = useState(false);

    // Live query untuk mendapatkan jumlah data pending di IndexedDB
    const pendingCount = useLiveQuery(
        () => db.sync_queue.where('status').equals('pending').count(),
        [],
        0
    );

    // Reset any stuck failed syncs back to pending on load
    useEffect(() => {
        db.sync_queue.where('status').equals('failed').modify({ status: 'pending', retry_count: 0 });
    }, []);

    useEffect(() => {
        const handleOnline = () => setIsOnline(true);
        const handleOffline = () => setIsOnline(false);

        window.addEventListener('online', handleOnline);
        window.addEventListener('offline', handleOffline);

        const syncInterval = setInterval(async () => {
            if (!navigator.onLine) return;

            try {
                // Ambil semua antrean yang statusnya pending
                const pendingItems = await db.sync_queue
                    .where('status')
                    .equals('pending')
                    .toArray();

                if (pendingItems.length > 0) {
                    setIsSyncing(true);
                } else {
                    setIsSyncing(false);
                    return;
                }

                // Urutkan id agar pelanggan disinkronkan lebih dulu jika mereka di-queue lebih awal
                pendingItems.sort((a, b) => (a.id ?? 0) - (b.id ?? 0));

                for (const item of pendingItems) {
                    try {
                        let response;
                        
                        if (item.type === 'customer') {
                            response = await axios.post('/api/customers/sync', {
                                payload: item.payload,
                            });
                            
                            if (response.status === 200 && response.data.real_id) {
                                const realId = response.data.real_id;
                                const offlineId = item.payload.offline_id;
                                
                                // Update pelanggan lokal agar is_synced true
                                await db.customers.update(offlineId, { is_synced: true });
                                
                                // Ganti offlineId dengan realId di semua order yang belum di-sync
                                const pendingOrders = await db.sync_queue
                                    .where('status')
                                    .equals('pending')
                                    .toArray();
                                    
                                for (const order of pendingOrders) {
                                    if (order.type === 'order' && order.payload.customer_id === offlineId) {
                                        const newPayload = { ...order.payload, customer_id: realId };
                                        if (order.id) {
                                            await db.sync_queue.update(order.id, { payload: newPayload });
                                        }
                                    }
                                }
                            }
                        } else {
                            response = await axios.post('/api/pos/sync', {
                                payload: item.payload,
                            });
                        }

                        if (response && response.status === 200) {
                            // Hapus jika sukses
                            if (item.id) {
                                await db.sync_queue.delete(item.id);
                            }
                        }
                    } catch (error) {
                        console.error(`Error syncing POS data (${item.type}):`, error);
                        // Jika gagal, naikkan retry_count
                        if (item.id) {
                            const newRetryCount = item.retry_count + 1;
                            await db.sync_queue.update(item.id, {
                                retry_count: newRetryCount,
                                status: 'pending', // NEVER fail silently, keep retrying
                            });
                        }
                    }
                }
            } catch (error) {
                console.error('Sync queue processor error:', error);
            } finally {
                setIsSyncing(false);
            }
        }, 5000); // Jalan setiap 5 detik

        return () => {
            clearInterval(syncInterval);
            window.removeEventListener('online', handleOnline);
            window.removeEventListener('offline', handleOffline);
        };
    }, []);

    return { isOnline, pendingCount, isSyncing };
}
