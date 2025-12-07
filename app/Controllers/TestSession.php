<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TestSession extends Controller
{
    public function index()
    {
        try {
            $session = session();
            $session->set('test', 'Hello World');
            echo "Session ID: " . session_id() . "<br>";
            echo "Test Value: " . $session->get('test');
        } catch (\Exception $e) {
            echo "Session Error: " . $e->getMessage();
        }
    }
}
