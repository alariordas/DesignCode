<?php
// Construye la ruta absoluta al archivo model/project.php usando __DIR__
require_once __DIR__ . '/../model/project.php';

class ProjectController {
    public function actualizarProyecto($repoId, $name, $ownerId, $images = [], $tags = []) {
        $updatedProject = Project::updateProjectByRepoId($repoId, $name, $ownerId, $images, $tags);

        if ($updatedProject) {
            echo "Proyecto actualizado exitosamente";
        } else {
            echo "Error al actualizar el proyecto o ese proyecto no existe";
        }
    }

    public function crearProyecto($repoId, $name, $ownerId, $images = [], $tags = [], $imageCategory = ['cover']) {
        $newProject = Project::postProject($repoId, $name, $ownerId, $images, $tags, $imageCategory);
    
        if ($newProject) {
            echo "Proyecto creado exitosamente";
        } else {
            echo "Error: El proyecto ya existe o no se pudo crear";
        }
    }
    

    // Método para obtener todos los proyectos
    public function getAllProjects() {
        $projects = Project::getAllProjects();

        if ($projects) {
            // Si se encontraron proyectos, retornar el listado
            return $projects;
        } else {
            // Si no se encontraron proyectos, retornar un mensaje de error o una lista vacía
            return [];
        }
    }
}
?>
