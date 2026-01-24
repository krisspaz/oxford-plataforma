import React, { useState } from 'react';
import { useTheme } from '../../../../contexts/ThemeContext';
import { FileText, UploadCloud, Trash2, Download, Search, Folder } from 'lucide-react';
import { toast } from 'sonner';

const DocumentManager = () => {
    const { darkMode } = useTheme();
    const [documents, setDocuments] = useState([
        { id: 1, name: 'Reglamento_Interno_2024.pdf', size: '2.4 MB', type: 'PDF', date: '2024-01-10' },
        { id: 2, name: 'Calendario_Escolar_Oficial.xlsx', size: '1.1 MB', type: 'XLSX', date: '2024-01-15' },
        { id: 3, name: 'Circular_001_Padres.docx', size: '500 KB', type: 'DOCX', date: '2024-02-01' },
    ]);

    const handleUpload = () => {
        // Simulation
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.onchange = (e) => {
            const file = e.target.files[0];
            if (file) {
                setDocuments(prev => [...prev, {
                    id: Date.now(),
                    name: file.name,
                    size: (file.size / 1024 / 1024).toFixed(2) + ' MB',
                    type: file.name.split('.').pop().toUpperCase(),
                    date: new Date().toISOString().split('T')[0]
                }]);
                toast.success('Documento subido al archivo digital');
            }
        };
        fileInput.click();
    };

    return (
        <div className={`h-full flex flex-col p-6 border rounded-2xl shadow-lg ${darkMode ? 'bg-gray-800 border-gray-700' : 'bg-white border-gray-100'}`}>
            <div className="flex justify-between items-center mb-6">
                <div>
                    <h3 className={`text-xl font-bold flex items-center gap-2 ${darkMode ? 'text-white' : 'text-gray-900'}`}>
                        <Folder className="text-yellow-500" />
                        Archivo Digital
                    </h3>
                    <p className={`text-sm ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Gestión centralizada de documentos.</p>
                </div>
                <div className="flex gap-3">
                    <div className="relative">
                        <Search className={`absolute left-3 top-2.5 ${darkMode ? 'text-gray-400' : 'text-gray-500'}`} size={16} />
                        <input
                            type="text"
                            placeholder="Buscar archivo..."
                            className={`pl-10 pr-4 py-2 rounded-lg border outline-none ${darkMode ? 'bg-gray-700 border-gray-600 text-white' : 'bg-gray-50 border-gray-200'}`}
                        />
                    </div>
                    <button
                        onClick={handleUpload}
                        className="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium"
                    >
                        <UploadCloud size={18} />
                        Subir
                    </button>
                </div>
            </div>

            <div className={`flex-1 overflow-y-auto rounded-xl border ${darkMode ? 'border-gray-700 bg-gray-900/50' : 'border-gray-100 bg-gray-50/50'}`}>
                <table className="w-full">
                    <thead className={`border-b ${darkMode ? 'border-gray-700 bg-gray-800' : 'border-gray-200 bg-gray-100'}`}>
                        <tr>
                            <th className={`text-left py-3 px-6 text-xs font-semibold uppercase tracking-wider ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Nombre</th>
                            <th className={`text-left py-3 px-6 text-xs font-semibold uppercase tracking-wider ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Tipo</th>
                            <th className={`text-left py-3 px-6 text-xs font-semibold uppercase tracking-wider ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Tamaño</th>
                            <th className={`text-left py-3 px-6 text-xs font-semibold uppercase tracking-wider ${darkMode ? 'text-gray-400' : 'text-gray-500'}`}>Fecha</th>
                            <th className="py-3 px-6"></th>
                        </tr>
                    </thead>
                    <tbody className={`divide-y ${darkMode ? 'divide-gray-700' : 'divide-gray-200'}`}>
                        {documents.map((doc) => (
                            <tr key={doc.id} className={`group ${darkMode ? 'hover:bg-gray-800' : 'hover:bg-white'} transition-colors`}>
                                <td className="py-4 px-6">
                                    <div className="flex items-center gap-3">
                                        <div className={`p-2 rounded ${darkMode ? 'bg-gray-700 text-gray-300' : 'bg-blue-50 text-blue-600'}`}>
                                            <FileText size={20} />
                                        </div>
                                        <span className={`font-medium ${darkMode ? 'text-white' : 'text-gray-900'}`}>{doc.name}</span>
                                    </div>
                                </td>
                                <td className={`py-4 px-6 text-sm ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>{doc.type}</td>
                                <td className={`py-4 px-6 text-sm ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>{doc.size}</td>
                                <td className={`py-4 px-6 text-sm ${darkMode ? 'text-gray-400' : 'text-gray-600'}`}>{doc.date}</td>
                                <td className="py-4 px-6 text-right opacity-0 group-hover:opacity-100 transition-opacity">
                                    <div className="flex justify-end gap-2">
                                        <button className="p-2 text-gray-400 hover:text-blue-500">
                                            <Download size={18} />
                                        </button>
                                        <button className="p-2 text-gray-400 hover:text-red-500">
                                            <Trash2 size={18} />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default DocumentManager;
