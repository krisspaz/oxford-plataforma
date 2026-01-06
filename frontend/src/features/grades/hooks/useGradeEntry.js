import { useState, useCallback, useEffect } from 'react';
import { gradeRecordService, bimesterService, teacherService } from '../../services'; // Adjust paths

export const useGradeEntry = () => {
    const [assignments, setAssignments] = useState([]); // Teacher's subjects
    const [bimesters, setBimesters] = useState([]);
    const [students, setStudents] = useState([]);

    const [selectedAssignmentId, setSelectedAssignmentId] = useState('');
    const [selectedBimesterId, setSelectedBimesterId] = useState('');

    const [loading, setLoading] = useState(false);
    const [saving, setSaving] = useState(false);
    const [currentBimester, setCurrentBimester] = useState(null);

    // Initial load
    useEffect(() => {
        const init = async () => {
            try {
                // 1. Load Bimesters
                const bRes = await bimesterService.getAll();
                if (bRes.success) {
                    setBimesters(bRes.data);
                    const active = bRes.data.find(b => !b.isClosed);
                    if (active) setSelectedBimesterId(active.id.toString());
                }

                // 2. Load Teacher Assignments
                const profile = await teacherService.getMyProfile();
                if (profile?.id) {
                    const asgs = await teacherService.getSubjects(profile.id);
                    setAssignments(asgs.map(a => ({
                        id: a.id,
                        name: `${a.subject.name} - ${a.grade.name} (${a.section?.name || 'A'})`,
                        gradeId: a.grade.id
                    })));
                }
            } catch (err) {
                console.error("Error init grade entry", err);
                // Fallback dev data
                if (process.env.NODE_ENV === 'development') {
                    setAssignments([
                        { id: '101', name: "Matemáticas - 5to Bachillerato (A)" },
                        { id: '102', name: "Física - 5to Bachillerato (A)" }
                    ]);
                }
            }
        };
        init();
    }, []);

    // Load students when selections change
    useEffect(() => {
        if (selectedAssignmentId && selectedBimesterId) {
            loadStudents();
        }
    }, [selectedAssignmentId, selectedBimesterId]);

    const loadStudents = async () => {
        setLoading(true);
        try {
            const res = await gradeRecordService.getByAssignmentAndBimester(
                parseInt(selectedAssignmentId),
                parseInt(selectedBimesterId)
            );

            if (res.success) {
                setCurrentBimester(res.bimester);
                setStudents((res.records || []).map(r => ({
                    id: r.id, // Record ID
                    studentId: r.student,
                    name: r.studentName,
                    carnet: r.studentCarnet,
                    score: r.score,
                    isLocked: r.isLocked
                })));
            }
        } catch (err) {
            console.error(err);
            // Demo data
            setStudents([
                { id: 1, studentId: 1, name: 'Juan Pérez', carnet: '2025-001', score: 85, isLocked: false },
                { id: 2, studentId: 2, name: 'María López', carnet: '2025-002', score: 92, isLocked: false },
            ]);
        } finally {
            setLoading(false);
        }
    };

    const updateScore = (studentId, newScore) => {
        setStudents(prev => prev.map(s =>
            s.id === studentId ? { ...s, score: newScore } : s
        ));
    };

    const saveGrades = async () => {
        setSaving(true);
        try {
            const payload = {
                assignmentId: parseInt(selectedAssignmentId),
                bimesterId: parseInt(selectedBimesterId),
                grades: students.map(s => ({
                    studentId: s.studentId,
                    score: s.score
                }))
            };
            await gradeRecordService.saveBatch(payload);
            return { success: true };
        } catch (err) {
            return { success: false, error: err.message };
        } finally {
            setSaving(false);
        }
    };

    return {
        assignments,
        bimesters,
        students,
        loading,
        saving,
        currentBimester,
        selections: {
            assignmentId: selectedAssignmentId,
            bimesterId: selectedBimesterId,
            setAssignmentId: setSelectedAssignmentId,
            setBimesterId: setSelectedBimesterId
        },
        actions: {
            updateScore,
            saveGrades
        }
    };
};
