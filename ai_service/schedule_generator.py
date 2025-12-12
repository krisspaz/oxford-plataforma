
import sys
import json
import random

def generate_schedule(data):
    """
    Generates a class schedule trying to minimize conflicts.
    Data expected:
    {
        "slots": ["Mon 8-9", "Mon 9-10", ...],
        "teachers": [{"id": 1, "name": "Mr. Smith", "subjects": ["Math"]}],
        "groups": [{"id": "1A", "subjects_needed": [{"subject": "Math", "hours": 5}]}]
    }
    """
    
    slots = data.get('slots', [])
    teachers = data.get('teachers', [])
    groups = data.get('groups', [])
    
    # Flatten needs into a list of "tasks" (group, subject, duration_index)
    tasks = []
    for group in groups:
        for req in group.get('subjects_needed', []):
            subject = req['subject']
            count = req['hours']
            for _ in range(count):
                tasks.append({
                    'group': group['id'],
                    'subject': subject
                })
                
    schedule = []
    # Simple randomized greedy algorithm
    # In a real production system, use 'deap' (Genetic Algo) or 'ortools' (Constraint Programming)
    
    # Shuffle tasks to vary result
    random.shuffle(tasks)
    
    # Track assignments to prevent conflicts
    # occupied[(slot_index, teacher_id)] = True
    # group_occupied[(slot_index, group_id)] = True
    
    teacher_occupied = set()
    group_occupied = set()
    
    assignments = []
    unassigned = []
    
    for task in tasks:
        assigned = False
        
        # Find capable teachers
        capable_teachers = [t for t in teachers if task['subject'] in t['subjects']]
        if not capable_teachers:
            unassigned.append({**task, "reason": "No teacher found"})
            continue
            
        # Try to find a slot
        # We iterate through slots randomly to distribute
        slot_indices = list(range(len(slots)))
        random.shuffle(slot_indices)
        
        for slot_idx in slot_indices:
            # Check if group is free
            if (slot_idx, task['group']) in group_occupied:
                continue
                
            # Try to find a free teacher for this slot
            random.shuffle(capable_teachers)
            selected_teacher = None
            
            for teacher in capable_teachers:
                if (slot_idx, teacher['id']) not in teacher_occupied:
                    selected_teacher = teacher
                    break
            
            if selected_teacher:
                # Assign
                teacher_occupied.add((slot_idx, selected_teacher['id']))
                group_occupied.add((slot_idx, task['group']))
                
                assignments.append({
                    'group': task['group'],
                    'subject': task['subject'],
                    'teacher_id': selected_teacher['id'],
                    'teacher_name': selected_teacher['name'],
                    'slot': slots[slot_idx],
                    'slot_index': slot_idx
                })
                assigned = True
                break
        
        if not assigned:
            unassigned.append({**task, "reason": "No available slot/teacher conflict"})

    return {
        "status": "success",
        "schedule": assignments,
        "unassigned_count": len(unassigned),
        "unassigned": unassigned
    }

if __name__ == "__main__":
    try:
        if len(sys.argv) > 1:
            input_json = sys.argv[1]
            data = json.loads(input_json)
            result = generate_schedule(data)
            print(json.dumps(result))
        else:
            # Demo Data if run without args
            demo_data = {
                "slots": ["Lunes 08:00", "Lunes 09:00", "Lunes 10:00", "Martes 08:00"],
                "teachers": [
                    {"id": 1, "name": "Profesor A", "subjects": ["Matemáticas"]},
                    {"id": 2, "name": "Profesor B", "subjects": ["Historia"]}
                ],
                "groups": [
                    {"id": "Grado 1", "subjects_needed": [{"subject": "Matemáticas", "hours": 2}, {"subject": "Historia", "hours": 1}]}
                ]
            }
            print(json.dumps(generate_schedule(demo_data)))
    except Exception as e:
        print(json.dumps({"error": str(e)}))
