<?php
    class ContactController {
        public function index() {
            $success = isset($_GET['success']);
            
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $this->sendContact($_POST);
            }
            
            require_once __DIR__ . '/../views/contact.php';
        }

        private function sendContact($data) {
            // Aquí puedes integrar con email o guardar en base de datos
            $name = htmlspecialchars($data['name']);
            $email = htmlspecialchars($data['email']);
            $subject = htmlspecialchars($data['subject']);
            $message = htmlspecialchars($data['message']);
            
            // Ejemplo: Guardar en base de datos (necesitarías crear la tabla)
            // $this->saveContactMessage($name, $email, $subject, $message);
            
            header('Location: /chilean_food_app/contact?success=1');
            exit;
        }
    }
?>