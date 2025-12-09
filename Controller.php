<?php

class Controller {

    /**
     * Load a view file.
     * This implementation searches upward from the current directory
     * to find a views folder (app/views, views, public/views), so it
     * works even if the project structure or working folder differ.
     *
     * @param string $view  e.g. 'dashboard/index'
     * @param array  $data  data to extract for the view
     */
    protected function view($view, $data = []) {
        // Make $data available in view safely (don't overwrite existing variables)
        extract($data, EXTR_SKIP);

        // Normalize relative filename (dashboard/index -> dashboard\index.php or linux sep)
        $relative = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $view);
        if (substr($relative, -4) !== '.php') {
            $relative .= '.php';
        }

        // Start searching from the directory of this core file, then go up parents
        $current = __DIR__; // core folder
        $foundBase = null;
        $attempted = [];

        // Limit search depth to avoid infinite loops (10 levels is plenty)
        for ($i = 0; $i < 12; $i++) {
            // candidate locations relative to current
            $candidates = [
                $current . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR,
                $current . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR,
                $current . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR,
                // also check sibling 'app/views' in parent (in case core is inside app)
                dirname($current) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR,
            ];

            foreach ($candidates as $base) {
                // normalize
                $base = rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
                if (isset($attempted[$base])) {
                    continue;
                }
                $attempted[$base] = file_exists($base) ? realpath($base) : false;
                if (is_dir($base)) {
                    $file = $base . $relative;
                    if (file_exists($file)) {
                        // include and return immediately
                        require $file;
                        return;
                    }
                }
            }

            // move one level up
            $parent = dirname($current);
            if ($parent === $current) {
                break; // reached filesystem root
            }
            $current = $parent;
        }

        // If not found via searching, also try the original conventional path as fallback
        $fallback = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $relative;
        $attempted[$fallback] = file_exists($fallback) ? realpath($fallback) : false;
        if (file_exists($fallback)) {
            require $fallback;
            return;
        }

        // Build helpful error message listing attempted folders and results
        $msg  = "View not found: {$view}\nTried the following base folders and files:\n\n";
        foreach ($attempted as $base => $exists) {
            $msg .= ($exists ? " - BASE: $base => FOUND at $exists\n" : " - BASE: $base => MISSING\n");
            // if base exists, show whether file exists too
            if ($exists) {
                $file = $exists . DIRECTORY_SEPARATOR . $relative;
                $msg .= "    Checked file: $file => " . (file_exists($file) ? "FOUND" : "MISSING") . "\n";
            }
            $msg .= "\n";
        }

        // Output a plain-text debug for development (change to Exception in production)
        header('Content-Type: text/plain; charset=utf-8', true, 500);
        echo nl2br(htmlspecialchars($msg, ENT_QUOTES, 'UTF-8'));
        exit;
    }

    /**
     * Load a model class from app/models
     */
    protected function model($model) {
        $modelPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'models' . DIRECTORY_SEPARATOR . $model . '.php';

        if (file_exists($modelPath)) {
            require_once $modelPath;
            if (class_exists($model)) {
                return new $model();
            } else {
                die("Model class not found after include: {$model}");
            }
        } else {
            die("Model not found: {$model}");
        }
    }

    /**
     * Redirect helper
     */
    protected function redirect($url) {
        header("Location: " . BASE_URL . $url);
        exit;
    }

    /**
     * JSON response helper
     */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }
}
