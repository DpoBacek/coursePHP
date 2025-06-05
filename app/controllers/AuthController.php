<?php
class AuthController {
    private $authService;
    
    public function __construct(AuthService $authService) {
        $this->authService = $authService;
    }

    public function login(Request $request) {
        $credentials = $request->only('username', 'password');
        
        if ($user = $this->authService->attemptLogin($credentials)) {
            $_SESSION['user'] = $user;
            redirect('/dashboard');
        }
        
        return view('auth/login', ['error' => 'Неверные учетные данные']);
    }

    public function logout() {
        $this->authService->logout();
        redirect('/login');
    }
}
?>