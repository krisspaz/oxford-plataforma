import jsPDF from 'jspdf';
import 'jspdf-autotable';
import * as XLSX from 'xlsx';

/**
 * Export Utilities for PDF and Excel
 * Used for: boletas, reportes, listas de estudiantes
 */

// ==========================================
// PDF EXPORT
// ==========================================

/**
 * Exportar tabla a PDF
 */
export const exportToPDF = (data, columns, filename = 'reporte', title = 'Reporte') => {
    const doc = new jsPDF();

    // Header
    doc.setFontSize(20);
    doc.setTextColor(59, 130, 246); // Blue
    doc.text(title, 14, 22);

    // Fecha
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text(`Generado: ${new Date().toLocaleDateString('es-GT')}`, 14, 30);

    // Table
    doc.autoTable({
        startY: 40,
        head: [columns.map(col => col.header)],
        body: data.map(row => columns.map(col => row[col.key] ?? '')),
        theme: 'striped',
        headStyles: {
            fillColor: [59, 130, 246],
            textColor: 255,
            fontStyle: 'bold'
        },
        styles: {
            fontSize: 10,
            cellPadding: 3
        },
        alternateRowStyles: {
            fillColor: [245, 247, 250]
        }
    });

    // Footer
    const pageCount = doc.internal.getNumberOfPages();
    for (let i = 1; i <= pageCount; i++) {
        doc.setPage(i);
        doc.setFontSize(8);
        doc.setTextColor(150);
        doc.text(
            `Página ${i} de ${pageCount}`,
            doc.internal.pageSize.getWidth() / 2,
            doc.internal.pageSize.getHeight() - 10,
            { align: 'center' }
        );
    }

    doc.save(`${filename}.pdf`);
};

/**
 * Exportar boleta de calificaciones
 */
export const exportBoletaPDF = (student, grades, period = 'Primer Bimestre') => {
    const doc = new jsPDF();

    // Header con logo
    doc.setFillColor(59, 130, 246);
    doc.rect(0, 0, 210, 35, 'F');

    doc.setTextColor(255);
    doc.setFontSize(24);
    doc.text('Colegio Oxford', 14, 18);

    doc.setFontSize(12);
    doc.text(`Boleta de Calificaciones - ${period}`, 14, 28);

    // Student info
    doc.setTextColor(0);
    doc.setFontSize(12);
    doc.text(`Estudiante: ${student.name}`, 14, 50);
    doc.text(`Grado: ${student.grade}`, 14, 58);
    doc.text(`Sección: ${student.section}`, 14, 66);
    doc.text(`Código: ${student.code}`, 120, 50);

    // Grades table
    doc.autoTable({
        startY: 75,
        head: [['Materia', 'Nota', 'Estado']],
        body: grades.map(g => [
            g.subject,
            g.grade,
            g.grade >= 60 ? '✓ Aprobado' : '✗ Reprobado'
        ]),
        theme: 'grid',
        headStyles: {
            fillColor: [59, 130, 246],
            textColor: 255
        },
        columnStyles: {
            1: { halign: 'center' },
            2: { halign: 'center' }
        },
        didParseCell: (data) => {
            if (data.section === 'body' && data.column.index === 2) {
                const grade = parseFloat(data.row.raw[1]);
                data.cell.styles.textColor = grade >= 60 ? [34, 197, 94] : [239, 68, 68];
            }
        }
    });

    // Promedio
    const promedio = grades.reduce((acc, g) => acc + g.grade, 0) / grades.length;
    const finalY = doc.lastAutoTable.finalY + 10;

    doc.setFontSize(14);
    doc.setTextColor(59, 130, 246);
    doc.text(`Promedio General: ${promedio.toFixed(2)}`, 14, finalY);

    // Footer
    doc.setFontSize(10);
    doc.setTextColor(100);
    doc.text(
        `Documento generado el ${new Date().toLocaleDateString('es-GT')}`,
        14,
        doc.internal.pageSize.getHeight() - 20
    );

    doc.save(`boleta_${student.code}_${period.replace(/\s/g, '_')}.pdf`);
};

/**
 * Exportar reporte de asistencia PDF
 */
export const exportAttendancePDF = (data, dateRange, className) => {
    const doc = new jsPDF('landscape');

    doc.setFontSize(18);
    doc.text(`Reporte de Asistencia - ${className}`, 14, 20);
    doc.setFontSize(10);
    doc.text(`Período: ${dateRange}`, 14, 28);

    doc.autoTable({
        startY: 35,
        head: [['Estudiante', 'Presente', 'Ausente', 'Tarde', '% Asistencia']],
        body: data.map(s => [
            s.name,
            s.present,
            s.absent,
            s.late,
            `${s.percentage}%`
        ]),
        theme: 'striped',
        headStyles: { fillColor: [34, 197, 94] }
    });

    doc.save(`asistencia_${className}_${dateRange.replace(/\//g, '-')}.pdf`);
};

// ==========================================
// EXCEL EXPORT
// ==========================================

/**
 * Exportar a Excel
 */
export const exportToExcel = (data, columns, filename = 'reporte') => {
    // Transform data
    const exportData = data.map(row => {
        const obj = {};
        columns.forEach(col => {
            obj[col.header] = row[col.key] ?? '';
        });
        return obj;
    });

    // Create workbook
    const ws = XLSX.utils.json_to_sheet(exportData);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Datos');

    // Style headers
    const range = XLSX.utils.decode_range(ws['!ref']);
    for (let C = range.s.c; C <= range.e.c; ++C) {
        const address = XLSX.utils.encode_col(C) + '1';
        if (!ws[address]) continue;
        ws[address].s = {
            font: { bold: true, color: { rgb: 'FFFFFF' } },
            fill: { fgColor: { rgb: '3B82F6' } }
        };
    }

    // Set column widths
    ws['!cols'] = columns.map(() => ({ wch: 20 }));

    XLSX.writeFile(wb, `${filename}.xlsx`);
};

/**
 * Exportar lista de estudiantes
 */
export const exportStudentListExcel = (students, className) => {
    const columns = [
        { header: 'Código', key: 'code' },
        { header: 'Nombre', key: 'name' },
        { header: 'Grado', key: 'grade' },
        { header: 'Sección', key: 'section' },
        { header: 'Email', key: 'email' },
        { header: 'Teléfono', key: 'phone' }
    ];

    exportToExcel(students, columns, `lista_estudiantes_${className}`);
};

/**
 * Exportar calificaciones a Excel
 */
export const exportGradesExcel = (grades, className, period) => {
    const columns = [
        { header: 'Código', key: 'code' },
        { header: 'Estudiante', key: 'name' },
        { header: 'Materia', key: 'subject' },
        { header: 'Nota', key: 'grade' },
        { header: 'Estado', key: 'status' }
    ];

    const data = grades.map(g => ({
        ...g,
        status: g.grade >= 60 ? 'Aprobado' : 'Reprobado'
    }));

    exportToExcel(data, columns, `calificaciones_${className}_${period}`);
};

// ==========================================
// UTILITY HOOKS
// ==========================================

/**
 * Hook para exportar
 */
export const useExport = () => {
    const exportPDF = (data, columns, filename, title) => {
        exportToPDF(data, columns, filename, title);
    };

    const exportExcel = (data, columns, filename) => {
        exportToExcel(data, columns, filename);
    };

    return { exportPDF, exportExcel };
};

export default {
    exportToPDF,
    exportToExcel,
    exportBoletaPDF,
    exportAttendancePDF,
    exportStudentListExcel,
    exportGradesExcel,
    useExport
};
