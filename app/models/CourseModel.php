<?php

namespace App\models;

use App\entities\Cours;
use App\entities\TextCourse;
use App\entities\User;
use App\entities\VideoCourse;
use App\config\Database;
use PDO, Exception;

class CourseModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function addCourse(Cours $course, int $userID, int $categoryID): bool {
        $query = "INSERT INTO courses (title, description, content_type, content_path, tags, categoryID, userid) 
              VALUES (:title, :description, :content_type, :content_path, :tags, :categoryid, :userid)";
        $stmt = $this->pdo->prepare($query);

        $tags = implode(',', $course->getTags());
        return $stmt->execute([
            ':title' => $course->getTitle(),
            ':description' => $course->getDescription(),
            ':content_type' => $course->getContentType(),
            ':content_path' => $course->getContent(),
            ':tags' => $tags,
            ':categoryid' => $categoryID,
            ':userid' => $userID,
        ]);
    }

    public function getCourses($limit = 8, $offset = 0): array {
        $limit = (int)$limit;
        $offset = (int)$offset;
        $sql = "SELECT courses.*, users.first_name, users.last_name, categories.category_name 
            FROM courses 
            INNER JOIN users ON courses.userid = users.userid 
            INNER JOIN categories ON courses.categoryid = categories.categoryid 
            LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $courses = [];
        foreach ($results as $row) {
            $publisher = new User(
                $row['userid'],
                $row['first_name'],
                $row['last_name']
            );
            $tags = !empty($row['tags']) ? explode(',', $row['tags']) : [];
            if ($row['content_type'] === 'video') {
                $course = new VideoCourse(
                    $row['courseid'],
                    $row['title'],
                    $row['description'],
                    $row['content_type'],
                    $row['content_path'],
                    $row['category_name'],
                    $publisher,
                    $row['status'],
                    $tags
                );
            } elseif ($row['content_type'] === 'text') {
                $course = new TextCourse(
                    $row['courseid'],
                    $row['title'],
                    $row['description'],
                    $row['content_type'],
                    $row['content_path'],
                    $row['category_name'],
                    $publisher,
                    $row['status'],
                    $tags
                );
            } else {
                throw new Exception("Unknown course type: " . $row['content_type']);
            }
            $courses[] = $course;
        }
        return $courses;
    }
    public function getTotalCourses(): int {
        $query = "SELECT COUNT(*) FROM courses";
        $stmt = $this->pdo->query($query);
        return $stmt->fetchColumn();
    }


    public function getCoursesByInstructor(int $userID): array {
        $query = "SELECT courses.*, categories.category_name, users.first_name, users.last_name 
              FROM courses 
              JOIN categories ON courses.categoryid = categories.categoryid 
              JOIN users ON courses.userid = users.userid 
              WHERE courses.userid = :userid ";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':userid' => $userID]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $courses = [];
        foreach ($results as $row) {
            $publisher = new User(
                $row['userid'],
                $row['first_name'],
                $row['last_name']
            );
            $tags = !empty($row['tags']) ? explode(',', $row['tags']) : [];
            if ($row['content_type'] === 'video') {
                $course = new VideoCourse(
                    $row['courseid'],
                    $row['title'],
                    $row['description'],
                    $row['content_type'],
                    $row['content_path'],
                    $row['category_name'],
                    $publisher,
                    $row['status'],
                    $tags
                );
            } elseif ($row['content_type'] === 'text') {
                $course = new TextCourse(
                    $row['courseid'],
                    $row['title'],
                    $row['description'],
                    $row['content_type'],
                    $row['content_path'],
                    $row['category_name'],
                    $publisher,
                    $row['status'],
                    $tags
                );
            } else {
                throw new \Exception("Unknown course type: " . $row['content_type']);
            }

            $courses[] = $course;
        }

        return $courses;
    }
    public function archiveCourse(int $courseID, int $userID): bool
    {
        $query = "UPDATE courses SET status = 'archived' WHERE courseid = :courseid AND userid = :userid";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':courseID' => $courseID,
            ':userid' => $userID,
        ]);
    }

    public function updateCourse(int $courseID, int $userID, array $data): bool
    {
        $query = "UPDATE courses SET title = :title, description = :description, categoryid = :categoryid, tags = :tags 
              WHERE courseid = :courseid AND userid = :userid";
        $stmt = $this->pdo->prepare($query);

        $tags = implode(',', $data['tags']);
        return $stmt->execute([
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':categoryid' => $data['categoryid'],
            ':tags' => $tags,
            ':courseID' => $courseID,
            ':userid' => $userID,
        ]);
    }
    public function getCourseById(int $courseID): ?Cours
    {
        $query = "SELECT courses.*, users.first_name, users.last_name, categories.category_name 
              FROM courses 
              INNER JOIN users ON courses.userid = users.userid 
              INNER JOIN categories ON courses.categoryid = categories.categoryid 
              WHERE courses.courseid = :courseid";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':courseID' => $courseID]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }
        $publisher = new User(
            $row['userid'],
            $row['first_name'],
            $row['last_name']
        );
        $tags = !empty($row['tags']) ? explode(',', $row['tags']) : [];
        if ($row['content_type'] === 'video') {
            return new VideoCourse(
                $row['courseid'],
                $row['title'],
                $row['description'],
                $row['content_type'],
                $row['content_path'],
                $row['category_name'],
                $publisher,
                $row['status'],
                $tags
            );
        } elseif ($row['content_type'] === 'text') {
            return new TextCourse(
                $row['courseid'],
                $row['title'],
                $row['description'],
                $row['content_type'],
                $row['content_path'],
                $row['category_name'],
                $publisher,
                $row['status'],
                $tags
            );
        }

        return null;
    }

    public function getCategoryName(int $categoryID): string {
        $query = "SELECT category_name FROM categories WHERE categoryid = :categoryid";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([':categoryid' => $categoryID]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['category_name'] ?? 'Uncategorized';
    }

    public function updateCourseStatus(int $courseID, int $userID, string $status): bool
    {
        $query = "UPDATE courses SET status = :status WHERE courseid = :courseid AND userid = :userid";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute([
            ':status' => $status,
            ':courseid' => $courseID,
            ':userid' => $userID,
        ]);
    }


    public function enrollStudent(int $courseID, int $userID): bool
    {
        $query = "SELECT * FROM enrollments WHERE courseid = :courseid AND userid = :userid";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['courseID' => $courseID, 'userid' => $userID]);

        if ($stmt->rowCount() > 0) {
            return false;
        }
        $query = "INSERT INTO enrollments (courseid, userid, status) VALUES (:courseID, :userid, 'enrolled')";
        $stmt = $this->pdo->prepare($query);
        return $stmt->execute(['courseID' => $courseID, 'userid' => $userID]);
    }

    public function getEnrolledCourses(int $userID, int $limit = 6, int $offset = 0): array
    {
        $query = "SELECT c.*, users.first_name, users.last_name, categories.category_name 
              FROM courses c
              JOIN enrollments e ON c.courseid = e.courseid
              JOIN users ON c.userid = users.userid
              JOIN categories ON c.categoryid = categories.categoryid
              WHERE e.userid = :userid
              LIMIT :limit OFFSET :offset";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':userid', $userID, PDO::PARAM_INT);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $courses = [];
        foreach ($results as $row) {
            $publisher = new User(
                $row['userid'],
                $row['first_name'],
                $row['last_name']
            );
            $tags = !empty($row['tags']) ? explode(',', $row['tags']) : [];
            if ($row['content_type'] === 'video') {
                $course = new VideoCourse(
                    $row['courseid'],
                    $row['title'],
                    $row['description'],
                    $row['content_type'],
                    $row['content_path'],
                    $row['category_name'],
                    $publisher,
                    $row['status'],
                    $tags
                );
            } elseif ($row['content_type'] === 'text') {
                $course = new TextCourse(
                    $row['courseid'],
                    $row['title'],
                    $row['description'],
                    $row['content_type'],
                    $row['content_path'],
                    $row['category_name'],
                    $publisher,
                    $row['status'],
                    $tags
                );
            } else {
                throw new \Exception("Unknown course type: " . $row['content_type']);
            }
            $courses[] = $course;
        }

        return $courses;
    }

    public function isStudentEnrolled(int $courseID, int $userID): bool
    {
        $query = "SELECT * FROM enrollments WHERE courseid = :courseid AND userid = :userid";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['courseID' => $courseID, 'userid' => $userID]);

        return $stmt->rowCount() > 0;
    }

    public function getTotalEnrolledCourses(int $userID): int
    {
        $query = "SELECT COUNT(*) 
              FROM enrollments 
              WHERE userid = :userid";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['userid' => $userID]);
        return $stmt->fetchColumn();
    }

    public function searchCourses(string $query): array
    {
        $query = "%$query%";
        $sql = "SELECT c.*, users.first_name, users.last_name, categories.category_name 
            FROM courses c
            JOIN users ON c.userid = users.userid
            JOIN categories ON c.categoryid = categories.categoryid
            WHERE c.title LIKE :query 
               OR c.description LIKE :query 
               OR users.first_name LIKE :query 
               OR users.last_name LIKE :query 
               OR categories.category_name LIKE :query";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['query' => $query]);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $courses = [];
        foreach ($results as $row) {
            $publisher = new User(
                $row['userid'],
                $row['first_name'],
                $row['last_name']
            );
            $tags = !empty($row['tags']) ? explode(',', $row['tags']) : [];
            if ($row['content_type'] === 'video') {
                $course = new VideoCourse(
                    $row['courseid'],
                    $row['title'],
                    $row['description'],
                    $row['content_type'],
                    $row['content_path'],
                    $row['category_name'],
                    $publisher,
                    $row['status'],
                    $tags
                );
            } elseif ($row['content_type'] === 'text') {
                $course = new TextCourse(
                    $row['courseid'],
                    $row['title'],
                    $row['description'],
                    $row['content_type'],
                    $row['content_path'],
                    $row['category_name'],
                    $publisher,
                    $row['status'],
                    $tags
                );
            } else {
                throw new \Exception("Unknown course type: " . $row['content_type']);
            }
            $courses[] = $course;
        }

        return $courses;
    }
}
