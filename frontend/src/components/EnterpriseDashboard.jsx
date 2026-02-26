import { useState, useEffect } from 'react';
import aiService from '../services/AiService';
import { Scale, Book, GitCommit, Zap } from 'lucide-react';

const EnterpriseDashboard = ({ visible, onClose }) => {
    const [activeTab, setActiveTab] = useState('audit');
    const [auditLog, setAuditLog] = useState('');
    // eslint-disable-next-line unused-imports/no-unused-vars
    const [metrics, setMetrics] = useState(null);
    const [simulation, setSimulation] = useState(null);
    const [maturity, setMaturity] = useState(null);

    useEffect(() => {
        if (visible) {
            // eslint-disable-next-line react-hooks/immutability
            loadData();
        }
    }, [visible]);

    const loadData = async () => {
        const [log, sim, mat] = await Promise.all([
            aiService.getAuditLog(),
            aiService.getFutureSimulation(),
            aiService.getMaturityIndex()
        ]);
        setAuditLog(log?.log || 'No activity recorded.');
        setSimulation(sim);
        setMaturity(mat);
    };

    if (!visible) return null;

    return (
        <div className="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div className="bg-gray-900 border border-gray-700 w-full max-w-5xl h-[80vh] rounded-2xl shadow-2xl flex flex-col overflow-hidden">

                {/* Header */}
                <div className="p-6 border-b border-gray-800 flex justify-between items-center bg-gray-950">
                    <div>
                        <h2 className="text-2xl font-bold text-white flex items-center gap-3">
                            <Shield className="text-indigo-400" />
                            Enterprise AI Command Center
                        </h2>
                        <p className="text-gray-400 text-sm mt-1">Institutional Intelligence & Governance System</p>
                    </div>
                    <button onClick={onClose} className="text-gray-400 hover:text-white transition">✖</button>
                </div>

                {/* Body */}
                <div className="flex flex-1 overflow-hidden">
                    {/* Sidebar */}
                    <div className="w-64 bg-gray-950 border-r border-gray-800 p-4 space-y-2">
                        <NavBtn id="audit" icon={GitCommit} label="Audit Log & Traceability" active={activeTab === 'audit'} onClick={setActiveTab} />
                        <NavBtn id="governance" icon={Scale} label="Rule Constitution" active={activeTab === 'governance'} onClick={setActiveTab} />
                        <NavBtn id="simulation" icon={Zap} label="Future Simulation" active={activeTab === 'simulation'} onClick={setActiveTab} />
                        <NavBtn id="ethics" icon={HeartIcon} label="Ethics & HR" active={activeTab === 'ethics'} onClick={setActiveTab} />
                        <NavBtn id="context" icon={Book} label="Institutional Memory" active={activeTab === 'context'} onClick={setActiveTab} />
                    </div>

                    {/* Content */}
                    <div className="flex-1 p-8 overflow-y-auto bg-gray-900">

                        {activeTab === 'audit' && (
                            <div className="space-y-4">
                                <h3 className="text-xl font-semibold text-white mb-4 flex items-center gap-2">
                                    <GitCommit size={20} className="text-blue-400" /> Decision Traceability
                                </h3>
                                <div className="bg-black/50 p-4 rounded-lg border border-gray-800 font-mono text-xs text-green-400 h-96 overflow-auto">
                                    <pre>{auditLog}</pre>
                                </div>
                                <div className="grid grid-cols-2 gap-4">
                                    <StatCard label="Decisions Logged" value="142" />
                                    <StatCard label="Hidden Cost Avoided" value="$4,200" color="text-green-400" />
                                </div>
                            </div>
                        )}

                        {activeTab === 'simulation' && simulation && (
                            <div className="space-y-6">
                                <h3 className="text-xl font-semibold text-white mb-4 flex items-center gap-2">
                                    <Zap size={20} className="text-yellow-400" /> 3-Year Projection
                                </h3>
                                <div className="grid grid-cols-3 gap-6">
                                    <StatCard label="Projected Enrollment" value={simulation.projected_enrollment} />
                                    <StatCard label="Classrooms Needed" value={simulation.needed_classrooms} />
                                    <StatCard label="Teacher Churn Risk" value={simulation.teacher_churn_risk} color="text-red-400" />
                                </div>
                                <div className="bg-indigo-900/20 p-6 rounded-xl border border-indigo-500/30">
                                    <h4 className="text-indigo-300 font-medium mb-2">Strategic Recommendation</h4>
                                    <p className="text-gray-300">Based on the +15% enrollment growth, it is recommended to start the hiring process for 2 new Math teachers in Q3 2026 to avoid schedule fragmentation.</p>
                                </div>
                            </div>
                        )}

                        {activeTab === 'context' && maturity && (
                            <div className="space-y-6">
                                <h3 className="text-xl font-semibold text-white mb-4 flex items-center gap-2">
                                    <Book size={20} className="text-purple-400" /> Institutional Context (LATAM)
                                </h3>
                                <div className="bg-gray-800 p-6 rounded-xl border border-gray-700 flex items-center justify-between">
                                    <div>
                                        <div className="text-gray-400 text-sm">Maturity Index (IMI)</div>
                                        <div className="text-4xl font-bold text-white mt-1">{maturity.imi_score}/100</div>
                                        <div className="text-blue-400 text-sm mt-1">{maturity.level}</div>
                                    </div>
                                    <div className="h-16 w-16 rounded-full border-4 border-blue-500 flex items-center justify-center text-xl font-bold">
                                        A+
                                    </div>
                                </div>
                                <div>
                                    <h4 className="text-white font-medium mb-3">Active Cultural Adaptations</h4>
                                    <div className="flex flex-wrap gap-2">
                                        <Badge text="Traffic Tolerance: High" />
                                        <Badge text="Local Holidays: Enabled" />
                                        <Badge text="Parent-Teacher Protocol: Strict" />
                                    </div>
                                </div>
                            </div>
                        )}

                        {activeTab === 'governance' && (
                            <div className="space-y-4">
                                <h3 className="text-xl font-semibold text-white mb-4 flex items-center gap-2">
                                    <Scale size={20} className="text-orange-400" /> Rule Constitution
                                </h3>
                                <div className="space-y-2">
                                    <RuleItem type="hard" text="Max Students per Class: 35" />
                                    <RuleItem type="hard" text="No Obsolete Labs Assignment" />
                                    <RuleItem type="legacy" text="Early Friday Exit (Tradition)" />
                                    <RuleItem type="soft" text="Prefer Morning Math Blocks" />
                                </div>
                            </div>
                        )}

                    </div>
                </div>
            </div>
        </div>
    );
};

// eslint-disable-next-line unused-imports/no-unused-vars
const NavBtn = ({ id, icon: Icon, label, active, onClick }) => (
    <button
        onClick={() => onClick(id)}
        className={`w-full flex items-center gap-3 px-4 py-3 rounded-lg text-sm font-medium transition-all ${active ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-500/20' : 'text-gray-400 hover:bg-gray-900 hover:text-white'
            }`}
    >
        <Icon size={18} />
        {label}
    </button>
);

// eslint-disable-next-line unused-imports/no-unused-vars
const StatCard = ({ label, value, color = "text-white" }) => (
    <div className="bg-gray-800/50 p-4 rounded-xl border border-gray-700">
        <div className="text-gray-400 text-xs uppercase tracking-wider">{label}</div>
        <div className={`text-2xl font-bold mt-1 ${color}`}>{value}</div>
    </div>
);

// eslint-disable-next-line unused-imports/no-unused-vars
const RuleItem = ({ type, text }) => {
    const colors = {
        hard: "border-red-500/50 text-red-200 bg-red-500/10",
        soft: "border-blue-500/50 text-blue-200 bg-blue-500/10",
        legacy: "border-amber-500/50 text-amber-200 bg-amber-500/10"
    };
    return (
        <div className={`p-3 rounded-lg border ${colors[type]} flex items-center justify-between`}>
            <span>{text}</span>
            <span className="text-xs font-bold uppercase px-2 py-1 rounded bg-black/20">{type} Rule</span>
        </div>
    );
};

// eslint-disable-next-line unused-imports/no-unused-vars
const Badge = ({ text }) => (
    <span className="px-3 py-1 rounded-full bg-gray-800 border border-gray-700 text-gray-300 text-xs">
        {text}
    </span>
);

// Mock icon for simplicity
const HeartIcon = ({ size, className }) => (
    <svg width={size} height={size} className={className} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" /></svg>
);

export default EnterpriseDashboard;
