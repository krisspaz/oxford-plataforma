<?php

namespace App\CQRS;

/**
 * CQRS Implementation
 * Command Query Responsibility Segregation
 * 
 * Commands: write operations (create, update, delete)
 * Queries: read operations (get, list, search)
 */

// ==========================================
// COMMAND BUS
// ==========================================

interface CommandInterface {}

interface CommandHandlerInterface
{
    public function handle(CommandInterface $command): mixed;
}

class CommandBus
{
    private array $handlers = [];

    public function register(string $commandClass, CommandHandlerInterface $handler): void
    {
        $this->handlers[$commandClass] = $handler;
    }

    public function dispatch(CommandInterface $command): mixed
    {
        $commandClass = get_class($command);
        
        if (!isset($this->handlers[$commandClass])) {
            throw new \RuntimeException("No handler for command: {$commandClass}");
        }

        return $this->handlers[$commandClass]->handle($command);
    }
}

// ==========================================
// QUERY BUS
// ==========================================

interface QueryInterface {}

interface QueryHandlerInterface
{
    public function handle(QueryInterface $query): mixed;
}

class QueryBus
{
    private array $handlers = [];

    public function register(string $queryClass, QueryHandlerInterface $handler): void
    {
        $this->handlers[$queryClass] = $handler;
    }

    public function dispatch(QueryInterface $query): mixed
    {
        $queryClass = get_class($query);
        
        if (!isset($this->handlers[$queryClass])) {
            throw new \RuntimeException("No handler for query: {$queryClass}");
        }

        return $this->handlers[$queryClass]->handle($query);
    }
}

// ==========================================
// STUDENT COMMANDS
// ==========================================

class CreateStudentCommand implements CommandInterface
{
    public function __construct(
        public readonly string $firstName,
        public readonly string $lastName,
        public readonly string $code,
        public readonly ?string $email = null,
        public readonly ?int $gradeId = null,
        public readonly ?int $sectionId = null,
    ) {}
}

class UpdateStudentCommand implements CommandInterface
{
    public function __construct(
        public readonly int $id,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $code = null,
        public readonly ?int $gradeId = null,
        public readonly ?int $sectionId = null,
    ) {}
}

class DeleteStudentCommand implements CommandInterface
{
    public function __construct(
        public readonly int $id
    ) {}
}

// ==========================================
// STUDENT QUERIES
// ==========================================

class GetStudentQuery implements QueryInterface
{
    public function __construct(
        public readonly int $id
    ) {}
}

class ListStudentsQuery implements QueryInterface
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $limit = 20,
        public readonly ?string $search = null,
        public readonly ?int $gradeId = null,
        public readonly ?string $orderBy = 'id',
        public readonly string $order = 'DESC',
    ) {}
}

class SearchStudentsQuery implements QueryInterface
{
    public function __construct(
        public readonly string $query,
        public readonly int $limit = 10,
    ) {}
}

// ==========================================
// GRADE COMMANDS
// ==========================================

class RecordGradeCommand implements CommandInterface
{
    public function __construct(
        public readonly int $studentId,
        public readonly int $subjectId,
        public readonly float $grade,
        public readonly string $period,
        public readonly ?string $comments = null,
    ) {}
}

class BulkRecordGradesCommand implements CommandInterface
{
    public function __construct(
        public readonly array $grades, // [{studentId, subjectId, grade, period}]
    ) {}
}

// ==========================================
// SCHEDULE COMMANDS
// ==========================================

class GenerateScheduleCommand implements CommandInterface
{
    public function __construct(
        public readonly int $academicYear,
        public readonly array $constraints = [],
    ) {}
}

class ApproveScheduleCommand implements CommandInterface
{
    public function __construct(
        public readonly int $scheduleId,
        public readonly int $approvedBy,
    ) {}
}

// ==========================================
// EXAMPLE HANDLER
// ==========================================

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Student;

class CreateStudentHandler implements CommandHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function handle(CommandInterface $command): Student
    {
        if (!$command instanceof CreateStudentCommand) {
            throw new \InvalidArgumentException('Invalid command type');
        }

        $student = new Student();
        $student->setCode($command->code);
        // Set other properties...

        $this->em->persist($student);
        $this->em->flush();

        return $student;
    }
}

class ListStudentsHandler implements QueryHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function handle(QueryInterface $query): array
    {
        if (!$query instanceof ListStudentsQuery) {
            throw new \InvalidArgumentException('Invalid query type');
        }

        $qb = $this->em->getRepository(Student::class)->createQueryBuilder('s');

        if ($query->search) {
            $qb->andWhere('s.code LIKE :search')
               ->setParameter('search', "%{$query->search}%");
        }

        if ($query->gradeId) {
            $qb->andWhere('s.grade = :gradeId')
               ->setParameter('gradeId', $query->gradeId);
        }

        return $qb
            ->orderBy("s.{$query->orderBy}", $query->order)
            ->setFirstResult(($query->page - 1) * $query->limit)
            ->setMaxResults($query->limit)
            ->getQuery()
            ->getResult();
    }
}
