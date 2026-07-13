import type { TransactionDetails } from "./ReceiptPrint";

export function printWithRawBT(t: TransactionDetails) {
    const formatRupiah = (num: number) => {
        return num.toLocaleString('id-ID');
    };

    let str = "";
    str += "      COTO SELERA RAKYAT      \n";
    str += "     Terima Kasih Atas        \n";
    str += "       Kunjungan Anda         \n";
    str += "--------------------------------\n";
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
    str += " Barang yang sudah dibeli tidak \n";
    str += "   dapat ditukar/dikembalikan   \n";
    str += "\n\n\n";

    // Encode text to Base64 to avoid URI character issues in intent
    // Actually RawBT supports plain text intent
    const encoded = encodeURI(str);
    
    // Gunakan intent RawBT
    window.location.href = `intent:${encoded}#Intent;scheme=rawbt;package=ru.a402d.rawbtprinter;end;`;
}
