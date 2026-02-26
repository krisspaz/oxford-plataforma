import { useState, useEffect } from 'react';
import { gradeRecordService, bimesterService, teacherService } from '../../../services'; // Adjust paths

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
            }
        };
        init();
    }, []);

    // Load students when selections change
    useEffect(() => {
        if (selectedAssignmentId && selectedBimesterId) {
            loadStudents();
        }
    // eslint-disable-next-line react-hooks/exhaustive-deps
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
            setStudents([]);
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
                assignmentId: parseInt(selectedAssignmentId), // Top level for ref
                bimesterId: parseInt(selectedBimesterId),
                records: students.map(s => ({
                    id: s.id || null, // Existing ID or null
                    studentId: s.studentId,
                    subjectAssignmentId: parseInt(selectedAssignmentId),
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
