export function calculateTotals(qty, rent) {

    let taxable = +(qty * rent).toFixed(2);
    let vat = +(taxable * 0.05).toFixed(2);
    let total = +(taxable + vat).toFixed(2);

    return { taxable, vat, total };
}