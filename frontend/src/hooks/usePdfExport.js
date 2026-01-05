import jsPDF from 'jspdf';
import 'jspdf-autotable';

export const usePdfExport = () => {

    // Configurable School Info
    const SCHOOL_NAME = "CORPORACIÓN EDUCACIONAL OXFORD";
    const SCHOOL_ADDRESS = "Ciudad de Guatemala";

    const createDoc = (title, subtitle, orientation = 'portrait') => {
        const doc = new jsPDF({ orientation });
        const pageWidth = doc.internal.pageSize.getWidth();

        // Header
        doc.setFontSize(18);
        doc.setTextColor(40, 40, 40);
        doc.text(SCHOOL_NAME, 14, 22);

        doc.setFontSize(14);
        doc.setTextColor(100, 100, 100);
        doc.text(title, 14, 32);

        if (subtitle) {
            doc.setFontSize(10);
            doc.text(subtitle, 14, 38);
        }

        // Line separator
        doc.setDrawColor(200, 200, 200);
        doc.line(14, 42, pageWidth - 14, 42);

        return doc;
    };

    const exportTable = ({ title, subtitle, columns, data, filename = 'reporte.pdf', orientation = 'portrait', autoTableOptions = {} }) => {
        const doc = createDoc(title, subtitle, orientation);

        doc.autoTable({
            head: [columns],
            body: data,
            startY: 45,
            styles: { fontSize: 9, cellPadding: 3 },
            headStyles: { fillColor: [66, 133, 244] },
            theme: 'grid',
            ...autoTableOptions
        });

        doc.save(filename);
    };

    return {
        createDoc,
        exportTable
    };
};
