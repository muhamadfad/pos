import { useState, useCallback, useEffect } from 'react';
import type { TransactionDetails } from '../Pages/point-of-sale/ReceiptPrint';

// Utilities for ESC/POS
const ESC = 0x1B;
const GS = 0x1D;

function encodeText(text: string): Uint8Array {
    // Basic ASCII encoder, sufficient for basic text
    // For more robust encoding, we could use TextEncoder but ESC/POS usually expects CP437 or similar
    const encoder = new TextEncoder();
    return encoder.encode(text);
}

function generateEscPosBuffer(t: TransactionDetails): Uint8Array {
    const formatRupiah = (num: number) => num.toLocaleString('id-ID');
    let str = "";
    
    // Init printer
    const init = new Uint8Array([ESC, 0x40]);
    
    // Center align
    const alignCenter = new Uint8Array([ESC, 0x61, 1]);
    str += "COTO SELERA RAKYAT\n";
    str += "Terima Kasih Atas\n";
    str += "Kunjungan Anda\n";
    str += "--------------------------------\n";
    
    // Left align
    const alignLeft = new Uint8Array([ESC, 0x61, 0]);
    if (t.invoiceNumber) {
        str += `No : ${t.invoiceNumber}\n`;
    }
    str += `Tgl: ${t.date}\n`;
    str += `Plg: ${t.customerName}\n`;
    str += "--------------------------------\n";
    
    t.cart.forEach(item => {
        str += `${item.name}\n`;
        const qtyPrice = `${item.qty}x ${formatRupiah(item.price)}`;
        const subtotal = `${formatRupiah(item.qty * item.price)}`;
        const spaces = Math.max(1, 32 - qtyPrice.length - subtotal.length);
        str += qtyPrice + " ".repeat(spaces) + subtotal + "\n";
    });
    
    str += "--------------------------------\n";
    
    const totalStr = formatRupiah(t.totalPrice);
    str += "Total: " + " ".repeat(Math.max(1, 32 - 7 - totalStr.length)) + totalStr + "\n";
    
    const paidStr = formatRupiah(t.paidAmount);
    str += "Bayar: " + " ".repeat(Math.max(1, 32 - 7 - paidStr.length)) + paidStr + "\n";
    
    if (t.change >= 0) {
        const changeStr = formatRupiah(t.change);
        str += "Kmbli: " + " ".repeat(Math.max(1, 32 - 7 - changeStr.length)) + changeStr + "\n";
    } else {
        const debtStr = formatRupiah(Math.abs(t.change));
        str += "Sisa : " + " ".repeat(Math.max(1, 32 - 7 - debtStr.length)) + debtStr + "\n";
    }
    
    str += "\n";
    
    // Center align
    str += " Barang yang sudah dibeli tidak \n";
    str += "   dapat ditukar/dikembalikan   \n";
    str += "\n\n\n\n";
    
    // Cut paper (GS V 0)
    const cut = new Uint8Array([GS, 0x56, 0x41, 0x00]); // partial cut
    
    const textBuffer = encodeText(str);
    
    // Combine all arrays
    const combined = new Uint8Array(
        init.length + alignCenter.length + textBuffer.length + alignLeft.length + cut.length
    );
    
    let offset = 0;
    combined.set(init, offset); offset += init.length;
    combined.set(alignCenter, offset); offset += alignCenter.length;
    combined.set(textBuffer, offset); offset += textBuffer.length;
    // Note: To be precise, we need to apply alignLeft before the left aligned text, 
    // but for simplicity we generated all text together. 
    // Let's refine the construction:
    
    // Actually, doing it step by step is better:
    const buffers: Uint8Array[] = [];
    
    buffers.push(new Uint8Array([ESC, 0x40])); // init
    buffers.push(new Uint8Array([ESC, 0x61, 1])); // center
    buffers.push(encodeText("      COTO SELERA RAKYAT      \n"));
    buffers.push(encodeText("     Terima Kasih Atas        \n"));
    buffers.push(encodeText("       Kunjungan Anda         \n"));
    buffers.push(encodeText("--------------------------------\n"));
    
    buffers.push(new Uint8Array([ESC, 0x61, 0])); // left
    if (t.invoiceNumber) {
        buffers.push(encodeText(`No : ${t.invoiceNumber}\n`));
    }
    buffers.push(encodeText(`Tgl: ${t.date}\n`));
    buffers.push(encodeText(`Plg: ${t.customerName}\n`));
    buffers.push(encodeText("--------------------------------\n"));
    
    t.cart.forEach(item => {
        buffers.push(encodeText(`${item.name}\n`));
        const qtyPrice = `${item.qty}x ${formatRupiah(item.price)}`;
        const subtotal = `${formatRupiah(item.qty * item.price)}`;
        const spaces = Math.max(1, 32 - qtyPrice.length - subtotal.length);
        buffers.push(encodeText(qtyPrice + " ".repeat(spaces) + subtotal + "\n"));
    });
    
    buffers.push(encodeText("--------------------------------\n"));
    buffers.push(encodeText("Total: " + " ".repeat(Math.max(1, 32 - 7 - totalStr.length)) + totalStr + "\n"));
    buffers.push(encodeText("Bayar: " + " ".repeat(Math.max(1, 32 - 7 - paidStr.length)) + paidStr + "\n"));
    
    if (t.change >= 0) {
        const changeStr = formatRupiah(t.change);
        buffers.push(encodeText("Kmbli: " + " ".repeat(Math.max(1, 32 - 7 - changeStr.length)) + changeStr + "\n"));
    } else {
        const debtStr = formatRupiah(Math.abs(t.change));
        buffers.push(encodeText("Sisa : " + " ".repeat(Math.max(1, 32 - 7 - debtStr.length)) + debtStr + "\n"));
    }
    buffers.push(encodeText("\n"));
    
    buffers.push(new Uint8Array([ESC, 0x61, 1])); // center
    buffers.push(encodeText(" Barang yang sudah dibeli tidak \n"));
    buffers.push(encodeText("   dapat ditukar/dikembalikan   \n"));
    buffers.push(encodeText("\n\n\n\n"));
    
    // Cut
    // buffers.push(new Uint8Array([GS, 0x56, 0x41, 0x00])); 
    
    const totalLength = buffers.reduce((acc, val) => acc + val.length, 0);
    const finalBuffer = new Uint8Array(totalLength);
    
    let currentOffset = 0;
    for (const b of buffers) {
        finalBuffer.set(b, currentOffset);
        currentOffset += b.length;
    }
    
    return finalBuffer;
}

// Global state to persist connection across rerenders
let cachedCharacteristic: any = null;
let cachedDevice: any = null;

export function useWebBluetoothPrinter() {
    const [isConnected, setIsConnected] = useState(false);
    const [isConnecting, setIsConnecting] = useState(false);
    const [printerName, setPrinterName] = useState<string | null>(null);

    const isSupported = typeof window !== 'undefined' && 'bluetooth' in window.navigator;

    useEffect(() => {
        if (cachedDevice && cachedDevice.gatt?.connected) {
            setIsConnected(true);
            setPrinterName(cachedDevice.name);
        }
    }, []);

    const connect = useCallback(async () => {
        if (!isSupported) {
            alert("Browser ini tidak mendukung Web Bluetooth. Gunakan Google Chrome di Android/PC.");
            return false;
        }

        try {
            setIsConnecting(true);
            const nav = window.navigator as any;
            
            const device = await nav.bluetooth.requestDevice({
                acceptAllDevices: true,
                optionalServices: [
                    '000018f0-0000-1000-8000-00805f9b34fb',
                    'e7810a71-73ae-499d-8c15-faa9aef0c3f2',
                    '49535343-fe7d-4ae5-8fa9-9fafd205e455'
                ]
            });

            const server = await device.gatt.connect();
            
            const services = await server.getPrimaryServices();
            let targetChar = null;
            
            for (const service of services) {
                const characteristics = await service.getCharacteristics();
                for (const char of characteristics) {
                    if (char.properties.write || char.properties.writeWithoutResponse) {
                        targetChar = char;
                        break;
                    }
                }
                if (targetChar) break;
            }

            if (!targetChar) {
                throw new Error("Tidak menemukan layanan cetak pada perangkat ini.");
            }

            cachedDevice = device;
            cachedCharacteristic = targetChar;
            
            device.addEventListener('gattserverdisconnected', () => {
                setIsConnected(false);
                setPrinterName(null);
                cachedDevice = null;
                cachedCharacteristic = null;
            });

            setIsConnected(true);
            setPrinterName(device.name || "Bluetooth Printer");
            setIsConnecting(false);
            return true;
        } catch (error) {
            console.error("Bluetooth connection error:", error);
            setIsConnecting(false);
            return false;
        }
    }, [isSupported]);

    const disconnect = useCallback(() => {
        if (cachedDevice && cachedDevice.gatt?.connected) {
            cachedDevice.gatt.disconnect();
        }
    }, []);

    const printReceipt = useCallback(async (transaction: TransactionDetails) => {
        if (!cachedCharacteristic) {
            console.warn("Printer tidak terhubung.");
            return false;
        }

        try {
            const buffer = generateEscPosBuffer(transaction);
            
            // Web Bluetooth characteristic value can only hold up to 512 bytes per write typically.
            // But we should chunk it into ~500 bytes to be safe.
            const CHUNK_SIZE = 400;
            for (let i = 0; i < buffer.length; i += CHUNK_SIZE) {
                const chunk = buffer.slice(i, i + CHUNK_SIZE);
                await cachedCharacteristic.writeValue(chunk);
            }
            return true;
        } catch (error) {
            console.error("Print error:", error);
            return false;
        }
    }, []);

    return {
        isSupported,
        isConnected,
        isConnecting,
        printerName,
        connect,
        disconnect,
        printReceipt
    };
}
