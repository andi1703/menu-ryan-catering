function exportToExcel(data) {
    // Convert data to Excel format and trigger download
    const worksheet = XLSX.utils.json_to_sheet(data);
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "Material Report");
    XLSX.writeFile(workbook, "material_report.xlsx");
}

function exportToPDF(data) {
    // Convert data to PDF format and trigger download
    const doc = new jsPDF();
    doc.text("Material Requirements Report", 10, 10);
    
    let y = 20;
    data.forEach(item => {
        doc.text(`${item.name}: ${item.quantity}`, 10, y);
        y += 10;
    });

    doc.save("material_report.pdf");
}

function exportToCSV(data) {
    // Convert data to CSV format and trigger download
    const csvContent = "data:text/csv;charset=utf-8," 
        + data.map(e => e.name + "," + e.quantity).join("\n");

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "material_report.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function setupExportButtons() {
    document.getElementById("export-excel").addEventListener("click", function() {
        const data = getDataForExport(); // Assume this function retrieves the data to export
        exportToExcel(data);
    });

    document.getElementById("export-pdf").addEventListener("click", function() {
        const data = getDataForExport(); // Assume this function retrieves the data to export
        exportToPDF(data);
    });

    document.getElementById("export-csv").addEventListener("click", function() {
        const data = getDataForExport(); // Assume this function retrieves the data to export
        exportToCSV(data);
    });
}

// Call setupExportButtons on page load
document.addEventListener("DOMContentLoaded", setupExportButtons);