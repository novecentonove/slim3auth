<?php

namespace App\Controllers\Auth;
use \App\Controllers\Controller;
use \App\Models\User;
use Respect\Validation\Validator as v;

class AuthController extends Controller
{
     /* Sign Up */
     public function getSignUp($request, $response){
          $this->container->view->render($response, 'auth/signup.twig');
     }
   
     public function postSignUp($request, $response){
          $validation = $this->container->validator->validate($request, [
               'name' => v::notEmpty()->alpha()->length(4, 20),
               'email' => v::noWhitespace()->notEmpty()->emailAvailable(),
               'password' => v::noWhitespace()->notEmpty()->length(6, null)
          ]);

          if($validation->failed($request, $response)){
               $_SESSION['old'] = $request->getParams();
               return $response->withRedirect($this->container->router->pathFor('auth.signup'));
          }

          
          $user = User::create([
               'name' => $request->getParam('name'),
               'email' => $request->getParam('email'),
               'password' => password_hash($request->getParam('password'), PASSWORD_DEFAULT)
          ]);

          $this->container->auth->attempt($user->email, $request->getParam('password'));

          $this->container->flash->addMessage('success', 'you have been sign up!');

          return $response->withRedirect($this->container->router->pathFor('home'));
   }



     /* Sign In */
     public function getSignIn($request, $response){
          $this->container->view->render($response, 'auth/signin.twig');
     }

     public function postSignIn($request, $response){
          $auth = $this->container->auth->attempt(
               $request->getParam('email'),
               $request->getParam('password')
          );

          if(!$auth){
               $_SESSION['old'] = $request->getParams();
               return $response->withRedirect($this->container->router->pathFor('auth.signin'));
          }

          $this->container->flash->addMessage('success', 'you have been sign in!');
          
          return $response->withRedirect($this->container->router->pathFor('home'));
     }

     /* Sign Out */
     public function signOut($request, $response){
          $this->container->auth->logout();
          return $response->withRedirect($this->container->router->pathFor('home'));
     }
   

     public function getChangePassWord($request, $response){
          $this->container->view->render($response, 'auth/change_password.twig');
     }

     public function postChangePassWord($request, $response){
          $validation = $this->container->validator->validate($request, [
               'password_old' => v::noWhiteSpace()->notEmpty()->matchesPassword($this->container->auth->user()->password),
               'password_new' => v::noWhiteSpace()->notEmpty()
          ]);

          if($validation->failed()){
               return $response->withRedirect($this->container->router->pathFor('auth.password'));
          }

          $this->container->auth->user()->setPassword($request->getParam('password_new'));

          $this->container->flash->addMessage('success', 'Password updated');

          return $response->withRedirect($this->container->router->pathFor('home'));
     }
}
