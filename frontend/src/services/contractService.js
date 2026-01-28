import api from './api';

/**
 * Contract Service
 * PERFORMANCE: Uses dynamic imports for jsPDF to avoid loading in initial bundle
 */

// Cached module reference
let jsPDFModule = null;

/**
 * Lazy-load jsPDF
 */
const loadJsPDF = async () => {
    if (!jsPDFModule) {
        const jspdf = await import('jspdf');
        jsPDFModule = jspdf.default;
    }
    return jsPDFModule;
};

const contractService = {
    getAll: async () => {
        try {
            const response = await api.get('/contracts');
            return { success: true, data: response.data?.['hydra:member'] || response.data || response };
        } catch (error) {
            console.error('Error fetching contracts:', error);
            return { success: false, message: error.message };
        }
    },

    generate: async (data) => {
        try {
            const response = await api.post('/contracts/generate', data);
            return { success: true, data: response.data || response };
        } catch (error) {
            console.error('Error generating contract:', error);
            return { success: false, message: error.message };
        }
    },

    getMyContracts: async () => {
        try {
            const response = await api.get('/contracts/my');
            return { success: true, data: response.data || response };
        } catch (error) {
            return { success: false, error };
        }
    },

    upload: async (contractId, file) => {
        try {
            const formData = new FormData();
            formData.append('file', file);
            const response = await api.post(`/contracts/${contractId}/upload`, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            return { success: true, data: response.data || response };
        } catch (error) {
            console.error('Error uploading signed contract:', error);
            return { success: false, message: error.message };
        }
    },

    /**
     * Genera el contrato oficial de adhesión por prestación de servicios educativos
     * Formato aprobado por DIACO según resolución DDC-___-2022
     */
    generateOfficialContract: async (contractData) => {
        const jsPDF = await loadJsPDF();

        const {
            // Datos del representante
            representante = {},
            // Datos del educando
            educando = {},
            // Datos de cuotas
            inscripcion = 600,
            colegiatura = 600,
            // Datos del ciclo
            ciclo = new Date().getFullYear(),
            // Datos adicionales
            correlativo = '001',
            municipio = 'Mixco',
            departamento = 'Guatemala',
            resolucionMineduc = 'DDC-123-2024',
            fechaResolucion = '15/01/2024',
            formaPago = 'anticipada',
            diasPago = 5
        } = contractData;

        const doc = new jsPDF();
        const pageWidth = doc.internal.pageSize.getWidth();
        const margin = 15;
        const contentWidth = pageWidth - (margin * 2);
        let y = 20;

        // Helper functions
        const addCenteredText = (text, fontSize = 12, isBold = false) => {
            doc.setFontSize(fontSize);
            doc.setFont('helvetica', isBold ? 'bold' : 'normal');
            doc.text(text, pageWidth / 2, y, { align: 'center' });
            y += fontSize * 0.5;
        };

        const addParagraph = (text, fontSize = 9) => {
            doc.setFontSize(fontSize);
            doc.setFont('helvetica', 'normal');
            const lines = doc.splitTextToSize(text, contentWidth);
            doc.text(lines, margin, y);
            y += lines.length * fontSize * 0.4 + 2;
        };

        const addBoldLabel = (label, value, fontSize = 9) => {
            doc.setFontSize(fontSize);
            doc.setFont('helvetica', 'bold');
            doc.text(label, margin, y);
            doc.setFont('helvetica', 'normal');
            doc.text(value, margin + doc.getTextWidth(label) + 2, y);
            y += fontSize * 0.5;
        };

        const checkNewPage = (neededSpace = 30) => {
            if (y > 270 - neededSpace) {
                doc.addPage();
                y = 20;
            }
        };

        // ============ ENCABEZADO ============
        addCenteredText('CONTRATO DE ADHESIÓN POR PRESTACIÓN DE SERVICIOS EDUCATIVOS', 11, true);
        y += 3;
        addCenteredText('OXFORD BILINGUAL SCHOOL', 14, true);
        y += 5;

        // Correlativo y resolución
        doc.setFontSize(9);
        doc.text(`Correlativo interno Contrato No. ${correlativo}`, margin, y);
        doc.text(`Aprobado según Resolución DIACO: DDC-___-2022`, pageWidth - margin - 60, y);
        y += 10;

        // ============ COMPARECIENTES ============
        const fechaContrato = new Date();
        const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

        addParagraph(`En el municipio de ${municipio}, del departamento de ${departamento}, el día ${fechaContrato.getDate()} del mes de ${meses[fechaContrato.getMonth()]} del año dos mil ${fechaContrato.getFullYear() - 2000}.`);

        y += 2;
        addParagraph(`NOSOTROS: Victoria Angelina López de Paz de cuarenta y ocho años de edad, casada, guatemalteca, secretaria y oficinista, de este domicilio, me identifico con Documento Personal de Identificación, Código Único de Identificación número 2558 59929 1601, extendido por el Registro Nacional de las Personas de la República de Guatemala, actúo en mi calidad de Administrador Único y Representante Legal del Centro Educativo denominado OXFORD BILINGUAL SCHOOL, ubicado en 2da. Calle 16-94, Zona 4, de este municipio, lo que acredito con Acta de Nombramiento con registro número 626402, folio 438, libro 777 de Auxiliares de Comercio del Registro Mercantil de la República.`);

        y += 3;
        addParagraph('Y por la otra parte:');
        y += 2;
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(9);
        doc.text('(DATOS DEL REPRESENTANTE DEL EDUCANDO)', margin, y);
        y += 6;

        // Datos del representante
        const nombreRepresentante = `${representante.nombres || '____________________'} ${representante.apellidos || '____________________'}`;
        addParagraph(nombreRepresentante);

        addParagraph(`de ${representante.edad || '____'} años de edad, ${representante.estadoCivil || '____________'}, ${representante.nacionalidad || 'guatemalteco(a)'}, ${representante.profesion || '________________________'}, de este domicilio, me identifico con:`);

        addParagraph(`Documento Personal de Identificación CUI: ${representante.dpi || '____ _____ ____'}`);

        addParagraph(`con residencia en: ${representante.direccion || '________________________________________________'}`);

        addParagraph(`con número de teléfono en casa ${representante.telefonoCasa || '_____________'}, oficina ${representante.telefonoOficina || '_____________'} y celular ${representante.celular || '_____________'}, correo electrónico ${representante.email || '________________________'}`);

        addParagraph('declarando que la información personal proporcionada, es de carácter confidencial. Los comparecientes aseguramos ser de los datos de identificación anotados, estar en el libre ejercicio de nuestros derechos civiles y la calidad que se ejercita es amplia y suficiente para la celebración del CONTRATO DE ADHESIÓN POR PRESTACIÓN DE SERVICIOS EDUCATIVOS, de conformidad con las siguientes cláusulas:');

        checkNewPage(40);
        y += 5;

        // ============ CLÁUSULA PRIMERA ============
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.text('PRIMERA: INFORMACIÓN DEL EDUCANDO Y SERVICIO EDUCATIVO CONTRATADO.', margin, y);
        y += 6;

        const nombreEducando = `${educando.nombres || '____________________'} ${educando.apellidos || '____________________'}`;
        addParagraph(nombreEducando);
        addParagraph(`quien cursará el ${educando.grado || '____________'} de ${educando.nivel || '____________'}`);
        addParagraph(`${educando.carrera || ''} ${educando.jornada || 'Jornada Matutina'}`);
        addParagraph(`Servicio(s) educativo(s) debidamente autorizado(s) por el Ministerio de Educación, de conformidad con ${resolucionMineduc}, emitida(s) por la Dirección Departamental de Educación de ${departamento}, misma(s) que se pone(n) a la vista.`);

        checkNewPage(40);
        y += 5;

        // ============ CLÁUSULAS SEGUNDA Y TERCERA ============
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.text('SEGUNDA: VOLUNTARIEDAD EN LA CONTRATACIÓN DEL SERVICIO.', margin, y);
        y += 5;
        addParagraph('Manifiesta el Representante del Educando que, conociendo la amplia oferta de instituciones privadas que prestan servicios educativos, de manera voluntaria y espontánea ha elegido al Centro Educativo para la educación académica del educando.');

        checkNewPage(30);
        y += 3;
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.text('TERCERA: PLAZO.', margin, y);
        y += 5;
        addParagraph(`El servicio educativo convenido en este contrato será válido para el ciclo escolar del año dos mil ${ciclo - 2000}, durante su vigencia no puede ser modificada ninguna de sus cláusulas, las que deberán cumplirse a cabalidad. El Representante del Educando y el Centro Educativo podrán suscribir un nuevo contrato para el ciclo escolar inmediato siguiente, en caso acuerden la continuidad del educando.`);

        // ============ NUEVA PÁGINA PARA CLÁUSULA CUARTA ============
        doc.addPage();
        y = 20;

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.text('CUARTA: DERECHOS DEL EDUCANDO Y SU REPRESENTANTE.', margin, y);
        y += 5;
        addParagraph('El Educando y su Representante como usuarios del servicio educativo contratado, tendrán derecho a:');

        const derechosClausula4 = [
            'a. La protección a la vida, salud y seguridad en la adquisición, consumo y uso de bienes y servicios.',
            'b. La libertad de Elección y Contratación: El Representante del Educando goza del derecho a elegir y contratar el Centro Educativo que se adecúe a sus necesidades y capacidades económicas.',
            'c. La información veraz, suficiente, clara y oportuna sobre los bienes y servicios.',
            'd. Utilizar el Libro de Quejas o el medio legalmente autorizado por la Dirección de Atención y Asistencia al Consumidor.',
            'e. Observancia a las leyes y reglamentos en materia educativa.'
        ];

        derechosClausula4.forEach(derecho => {
            checkNewPage(15);
            addParagraph(derecho);
        });

        checkNewPage(40);
        y += 5;

        // ============ CLÁUSULA QUINTA ============
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.text('QUINTA: OBLIGACIONES DEL REPRESENTANTE DEL EDUCANDO.', margin, y);
        y += 5;

        const obligaciones = [
            'a. Pagar al Centro Educativo por los servicios proporcionados en el tiempo, modo y condiciones establecidas.',
            'b. Utilizar los bienes y servicios en observancia a su uso normal y cumplir con las condiciones pactadas.',
            'c. Ser orientadores en el proceso educativo de los educandos.'
        ];

        obligaciones.forEach(ob => {
            checkNewPage(12);
            addParagraph(ob);
        });

        // ============ CLÁUSULA SEXTA - CUOTAS ============
        doc.addPage();
        y = 20;

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.text('SEXTA: CUOTAS.', margin, y);
        y += 5;
        addParagraph('Como Representante del Educando me comprometo a efectuar únicamente los siguientes pagos, sin necesidad de cobro, ni requerimiento alguno:');

        y += 5;

        // Tabla de cuotas
        doc.setFillColor(240, 240, 240);
        doc.rect(margin, y, contentWidth, 8, 'F');
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(9);
        doc.text('EN CONCEPTO DE:', margin + 5, y + 5);
        doc.text('LA CANTIDAD DE:', pageWidth - margin - 50, y + 5);
        y += 10;

        doc.setFont('helvetica', 'normal');
        doc.text('a) INSCRIPCIÓN POR EDUCANDO (UN SÓLO PAGO ANUAL):', margin + 5, y + 5);
        doc.text(`Q. ${inscripcion.toLocaleString('es-GT', { minimumFractionDigits: 2 })}`, pageWidth - margin - 30, y + 5);
        y += 8;

        doc.text('b) COLEGIATURA MENSUAL (10 CUOTAS DE ENERO A OCTUBRE):', margin + 5, y + 5);
        doc.text(`Q. ${colegiatura.toLocaleString('es-GT', { minimumFractionDigits: 2 })}`, pageWidth - margin - 30, y + 5);
        y += 12;

        addParagraph(`Cuotas debidamente autorizadas por el Ministerio de Educación, según resolución No. ${resolucionMineduc}, de fecha ${fechaResolucion}, emitida por la Dirección Departamental de ${departamento}.`);

        y += 3;

        // Tabla de niveles
        doc.setFillColor(240, 240, 240);
        doc.rect(margin, y, contentWidth, 8, 'F');
        doc.setFont('helvetica', 'bold');
        doc.setFontSize(8);
        doc.text('NIVEL DE EDUCACIÓN', margin + 20, y + 5);
        doc.text('INSCRIPCIÓN', margin + 80, y + 5);
        doc.text('COLEGIATURA MENSUAL', margin + 120, y + 5);
        y += 10;

        doc.setFont('helvetica', 'normal');
        const niveles = [
            { nivel: 'Pre-primaria', insc: 600, col: 550 },
            { nivel: 'Primaria', insc: 600, col: 600 },
            { nivel: 'Otros', insc: 650, col: 600 }
        ];

        niveles.forEach(n => {
            doc.text(n.nivel, margin + 20, y + 4);
            doc.text(`Q. ${n.insc.toFixed(2)}`, margin + 80, y + 4);
            doc.text(`Q. ${n.col.toFixed(2)}`, margin + 130, y + 4);
            y += 7;
        });

        y += 5;
        addParagraph(`Para el pago de las cuotas, ambas partes acordamos que sea en forma ${formaPago}, debiendo efectuar el pago durante ${diasPago} días hábiles del mes al cual corresponde el servicio educativo brindado.`);

        // ============ CLÁUSULAS RESTANTES ============
        checkNewPage(60);
        y += 5;

        const clausulasRestantes = [
            { titulo: 'SÉPTIMA: INCUMPLIMIENTO DEL PAGO.', texto: 'En caso que el Representante del Educando se atrase o incumpla con los pagos normados en la cláusula anterior, el Centro Educativo podrá exigir al Representante del Educando el cumplimiento de las obligaciones contraídas en el presente contrato.' },
            { titulo: 'NOVENA: CHEQUES RECHAZADOS.', texto: 'Por concepto de cheques rechazados el Centro Educativo podrá cobrar como máximo el valor que por tal motivo debita o cobra el Banco que rechazó el pago del mismo.' },
            { titulo: 'DÉCIMA: TRASLADO O RETIRO DEL EDUCANDO.', texto: 'El traslado o retiro del educando podrá realizarse en cualquier momento del ciclo escolar. El Representante del Educando debe cancelar la cuota mensual hasta el mes en que efectivamente sea retirado el educando del plantel educativo.' },
            { titulo: 'DÉCIMA PRIMERA: COPIA DEL CONTRATO.', texto: 'El Centro Educativo deberá entregar una copia del presente contrato, quedando el original en poder de la autoridad del mismo.' },
            { titulo: 'DÉCIMA SEGUNDA: DERECHO DE RETRACTO.', texto: 'El Representante del Educando tendrá derecho a retractarse dentro de un plazo no mayor de cinco días hábiles, contados a partir de la firma del contrato.' }
        ];

        clausulasRestantes.forEach(cl => {
            checkNewPage(25);
            doc.setFont('helvetica', 'bold');
            doc.setFontSize(9);
            doc.text(cl.titulo, margin, y);
            y += 5;
            addParagraph(cl.texto);
            y += 3;
        });

        // ============ CLÁUSULA FINAL Y FIRMAS ============
        doc.addPage();
        y = 20;

        doc.setFont('helvetica', 'bold');
        doc.setFontSize(10);
        doc.text('DÉCIMA TERCERA: ACEPTACIÓN.', margin, y);
        y += 6;
        addParagraph('Nosotros los comparecientes, damos lectura íntegra al presente contrato, enterados de su contenido, objeto, validez y demás efectos legales, lo ratificamos, aceptamos y firmamos.');

        y += 40;

        // Líneas de firma
        const firmaY = y;
        doc.setDrawColor(0);
        doc.line(margin + 10, firmaY, margin + 70, firmaY);
        doc.line(pageWidth - margin - 70, firmaY, pageWidth - margin - 10, firmaY);

        doc.setFontSize(8);
        doc.setFont('helvetica', 'normal');
        doc.text('f) Representante Legal o Propietario', margin + 15, firmaY + 5);
        doc.text('f) Representante del Educando', pageWidth - margin - 65, firmaY + 5);

        doc.text('Victoria Angelina López de Paz', margin + 15, firmaY + 12);
        doc.text(nombreRepresentante, pageWidth - margin - 65, firmaY + 12);

        // Footer
        y = 280;
        doc.setFontSize(7);
        doc.setTextColor(128, 128, 128);
        doc.text('OXFORD BILINGUAL SCHOOL - 2da. Calle 16-94, Zona 4, Mixco, Guatemala', pageWidth / 2, y, { align: 'center' });
        doc.text(`Contrato generado el ${fechaContrato.toLocaleDateString('es-GT')}`, pageWidth / 2, y + 4, { align: 'center' });

        return doc;
    },

    downloadPdf: async (id, contractData = null) => {
        try {
            // Si hay datos completos, generar contrato oficial
            if (contractData && contractData.representante) {
                const doc = await contractService.generateOfficialContract(contractData);
                const fileName = `contrato_${contractData.educando?.apellidos || 'estudiante'}_${contractData.ciclo || new Date().getFullYear()}.pdf`;
                doc.save(fileName);
                return { success: true };
            }

            // Si solo tenemos datos básicos, generar versión simple del contrato
            let contract = contractData;

            if (!contract) {
                const response = await api.get(`/contracts/${id}/download`);
                if (response.success && response.data) {
                    contract = response.data;
                } else {
                    contract = {
                        id,
                        studentName: 'Estudiante',
                        cycleName: new Date().getFullYear().toString(),
                        status: 'PENDING',
                        createdAt: new Date().toISOString().split('T')[0]
                    };
                }
            }

            // Generar contrato oficial con datos básicos
            const doc = await contractService.generateOfficialContract({
                educando: {
                    nombres: contract.studentName?.split(' ')[0] || '',
                    apellidos: contract.studentName?.split(' ').slice(1).join(' ') || '',
                    grado: contract.grade || '',
                    nivel: contract.level || 'Primaria',
                    jornada: 'Jornada Matutina'
                },
                representante: {
                    nombres: contract.parentName?.split(' ')[0] || '',
                    apellidos: contract.parentName?.split(' ').slice(1).join(' ') || '',
                    dpi: contract.parentDpi || '',
                    celular: contract.parentPhone || '',
                    email: contract.parentEmail || ''
                },
                ciclo: parseInt(contract.cycleName) || new Date().getFullYear(),
                correlativo: String(contract.id).padStart(3, '0')
            });

            doc.save(`contrato_${(contract.studentName || 'estudiante').replace(/\s+/g, '_')}_${contract.cycleName || 'ciclo'}.pdf`);

            return { success: true };
        } catch (error) {
            console.error('Error downloading contract PDF:', error);
            return { success: false, message: error.message };
        }
    }
};

export default contractService;
