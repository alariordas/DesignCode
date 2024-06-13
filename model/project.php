<?php
// model/Project.php

require_once 'database.php';

class Project {
    private $repoId;
    private $name;
    private $ownerId;
    private $images;
    private $tags;

    public function __construct($repoId, $name, $ownerId, $images = [], $tags = []) {
        $this->repoId = $repoId;
        $this->name = $name;
        $this->ownerId = $ownerId;
        $this->images = $images;
        $this->tags = $tags;
    }

    public function getRepoId() {
        return $this->repoId;
    }

    public function getName() {
        return $this->name;
    }

    public function getOwnerId() {
        return $this->ownerId;
    }

    public function getImages() {
        return $this->images;
    }

    public function getTags() {
        return $this->tags;
    }

    public static function getAllProjects() {
        $conn = conectar();

        $sql = "SELECT * FROM projects";
        $result = $conn->query($sql);

        $projects = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $images = self::getImagesByProjectId($row['RepoId']);
                $tags = self::getTagsByProjectId($row['RepoId']);
                $projects[] = new Project($row['RepoId'], $row['Name'], $row['OwnerId'], $images, $tags);
            }
        }

        desconectar($conn);

        return $projects;
    }

    public static function getProjectByRepoId($repoId) {
        $conn = conectar();

        $safeRepoId = $conn->real_escape_string($repoId);

        $sql = "SELECT * FROM projects WHERE RepoId = '$safeRepoId'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $images = self::getImagesByProjectId($row['RepoId']);
            $tags = self::getTagsByProjectId($row['RepoId']);
            $project = new Project($row['RepoId'], $row['Name'], $row['OwnerId'], $images, $tags);
        } else {
            $project = null;
        }

        desconectar($conn);

        return $project;
    }

    public static function postProject($repoId, $name, $ownerId, $images = [], $tags = [], $imageCategories = []) {
        $conn = conectar();
    
        $safeRepoId = $conn->real_escape_string($repoId);
        $safeName = $conn->real_escape_string($name);
        $safeOwnerId = $conn->real_escape_string($ownerId);
    
        $existingProject = self::getProjectByRepoId($repoId);
    
        if ($existingProject) {
            desconectar($conn);
            return null;
        }
    
        $sql = "INSERT INTO projects (RepoId, Name, OwnerId) VALUES ('$safeRepoId', '$safeName', '$safeOwnerId')";
        $result = $conn->query($sql);
    
        if ($result) {
            foreach ($images as $index => $image) {
                $imageCategory = isset($imageCategories[$index]) ? $imageCategories[$index] : null;
                self::addImageToProject($repoId, $image, $imageCategory);
            }
            foreach ($tags as $tag) {
                self::addTagToProject($repoId, $tag);
            }
            $newProject = new Project($repoId, $name, $ownerId, $images, $tags);
        } else {
            $newProject = null;
        }
    
        desconectar($conn);
    
        return $newProject;
    }
    
    private static function addImageToProject($projectId, $filename, $category = null) {
        $conn = conectar();
        $safeProjectId = $conn->real_escape_string($projectId);
        $safeFilename = $conn->real_escape_string($filename);
        $safeCategory = $conn->real_escape_string($category);
    
        $sql = "INSERT INTO images (Filename, ProjectId, Category) VALUES ('$safeFilename', '$safeProjectId', '$safeCategory')";
        $conn->query($sql);
    
        desconectar($conn);
    }
    

    public static function updateProjectByRepoId($repoId, $name, $ownerId, $images = [], $tags = []) {
        $conn = conectar();

        $safeRepoId = $conn->real_escape_string($repoId);
        $safeName = $conn->real_escape_string($name);
        $safeOwnerId = $conn->real_escape_string($ownerId);

        $existingProject = self::getProjectByRepoId($repoId);
        if (!$existingProject) {
            desconectar($conn);
            return null;
        }

        $sql = "UPDATE projects SET Name = '$safeName', OwnerId = '$safeOwnerId' WHERE RepoId = '$safeRepoId'";
        $result = $conn->query($sql);

        if ($result) {
            self::clearProjectImages($repoId);
            self::clearProjectTags($repoId);

            foreach ($images as $image) {
                self::addImageToProject($repoId, $image);
            }
            foreach ($tags as $tag) {
                self::addTagToProject($repoId, $tag);
            }
            $updatedProject = new Project($repoId, $name, $ownerId, $images, $tags);
        } else {
            $updatedProject = null;
        }

        desconectar($conn);

        return $updatedProject;
    }

    private static function getImagesByProjectId($projectId) {
        $conn = conectar();
        $safeProjectId = $conn->real_escape_string($projectId);

        $sql = "SELECT Filename FROM images WHERE ProjectId = '$safeProjectId'";
        $result = $conn->query($sql);

        $images = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $images[] = $row['Filename'];
            }
        }

        desconectar($conn);
        return $images;
    }

    private static function getTagsByProjectId($projectId) {
        $conn = conectar();
        $safeProjectId = $conn->real_escape_string($projectId);

        $sql = "SELECT tags.Name FROM tags
                INNER JOIN project_tags ON tags.Id = project_tags.TagId
                WHERE project_tags.ProjectId = '$safeProjectId'";
        $result = $conn->query($sql);

        $tags = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $tags[] = $row['Name'];
            }
        }

        desconectar($conn);
        return $tags;
    }

    private static function addTagToProject($projectId, $tagName) {
        $conn = conectar();
        $safeProjectId = $conn->real_escape_string($projectId);
        $safeTagName = $conn->real_escape_string($tagName);

        // Check if the tag already exists
        $sql = "SELECT Id FROM tags WHERE Name = '$safeTagName'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $tagId = $row['Id'];
        } else {
            // Insert the new tag
            $sql = "INSERT INTO tags (Name) VALUES ('$safeTagName')";
            $result = $conn->query($sql);
            $tagId = $conn->insert_id;
        }

        // Associate the tag with the project
        $sql = "INSERT INTO project_tags (ProjectId, TagId) VALUES ('$safeProjectId', '$tagId')";
        $conn->query($sql);

        desconectar($conn);
    }

    private static function clearProjectImages($projectId) {
        $conn = conectar();
        $safeProjectId = $conn->real_escape_string($projectId);

        $sql = "DELETE FROM images WHERE ProjectId = '$safeProjectId'";
        $conn->query($sql);

        desconectar($conn);
    }

    private static function clearProjectTags($projectId) {
        $conn = conectar();
        $safeProjectId = $conn->real_escape_string($projectId);

        $sql = "DELETE FROM project_tags WHERE ProjectId = '$safeProjectId'";
        $conn->query($sql);

        desconectar($conn);
    }

    public static function getImagesByProjectAndCategory($projectId, $category = null) {
        $conn = conectar();
        $safeProjectId = $conn->real_escape_string($projectId);
        $safeCategory = $conn->real_escape_string($category);
    
        $sql = "SELECT Filename FROM images WHERE ProjectId = '$safeProjectId'";
        if ($category !== null) {
            $sql .= " AND Category = '$safeCategory'";
        }
        $result = $conn->query($sql);
    
        $images = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $images[] = $row['Filename'];
            }
        }
    
        desconectar($conn);
        return $images;
    }
    
}
