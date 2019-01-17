<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * User controller.
 *
 * @Route("")
 */
class UserController extends Controller
{
    /**
     * Lists all user entities.
     *
     * @Route("user/", name="user_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $token = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccessAdMod($token);

        if(!$hasAccess) return $this->redirectToRoute('homepage');

        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser()->getRole();
        $em = $this->getDoctrine()->getManager();

        if ($loggedUserRole === 'admin') {
            $users = $em->getRepository('AppBundle:User')->findAll();
            $cityName = '';
        }
        if ($loggedUserRole === 'moderator') {
            $city = $this->getUser()->getCity()->getId();
            $cityName = $this->getUser()->getCity()->getName();
            $cinemas = $em->getRepository('AppBundle:Cinema')->findBy(array('city' => $city));
            $users = $em->getRepository('AppBundle:User')->findBy(array('cinema' => $cinemas));
        }
        return $this->render('user/index.html.twig', array(
            'users' => $users,
            'city' => $cityName,
        ));
    }

    /**
     * Creates a new user entity.
     *
     * @Route("/register", name="user_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, \Swift_Mailer $mailer)
    {
        $user = new User();
        $form = $this->createForm('AppBundle\Form\UserType', $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $passwordHash = password_hash($user->getPassword(),PASSWORD_BCRYPT);
            $activationToken = hash("sha256", $user->getEmail());
            $user->setPassword($passwordHash);
            $user->setEmailActivate(0);
            $user->setActivationToken($activationToken);
            $user->setRole('client');
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $message = (new \Swift_Message('Registration'))
                ->setFrom('ticketmaniac2018@gmail.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'Emails/register.html.twig', array(
                            'name' => $user->getName(),
                            'activationToken' => $user->getActivationToken()
                        )
                    ),
                    'text/html'
                );

            $mailer->send($message);

            return $this->redirectToRoute('login');
        }

        return $this->render('register/register.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Login user
     *
     * @Route("/login", name="login")
     */
    public function loginAction(Request $request, AuthenticationUtils $authUtils)
    {
        $errors = $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

           return $this->render('login/login.html.twig', array(
               'errors' => $errors,
               'email' => $lastUsername
           ));
    }

    /**
     * Logout user
     *
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {

    }

    /**
     * Finds and displays a user entity.
     *
     * @Route("user/{id}", name="user_show")
     * @Method("GET")
     */
    public function showAction(User $user)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccessAdMod($loggedUserRole);

        if(!$hasAccess) return $this->redirectToRoute('homepage');

        $deleteForm = $this->createDeleteForm($user);
        return $this->render('user/show.html.twig', array(
            'user' => $user,
            'delete_form' => $deleteForm->createView(),
            'loggedUserRole' => $loggedUserRole,
        ));
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("user/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user, \Swift_Mailer $mailer, $id)
    {
        $token = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccess($token);

        if(!$hasAccess) return $this->redirectToRoute('homepage');

        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser()->getRole();
        $loggedUserId = $this->get('security.token_storage')->getToken()->getUser()->getId();

        $deleteForm = $this->createDeleteForm($user);
        $editForm = $this->createForm('AppBundle\Form\UserUpdateType');

        if (
        ($loggedUserRole === 'supervisior' && strval($loggedUserId) !== $id) ||
        ($loggedUserRole === 'client' && strval($loggedUserId) !== $id)
        ) return $this->redirectToRoute('homepage');

        if ($loggedUserRole !== 'admin' && strval($loggedUserId) === $id) {

            $editUserForm = $this->createForm('AppBundle\Form\UserUpdateType');
            $editUserForm->handleRequest($request);

            if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {
                if ($editUserForm->getData()['email'] == "") {
                    if ($this->isOldPasswordCorrect($editUserForm->getData()['oldPassword'])) {
                        $passwordHash = password_hash($editUserForm->getData()['newPassword'], PASSWORD_BCRYPT);
                        $user->setPassword($passwordHash);
                        $this->getDoctrine()->getManager()->flush();
                        return $this->redirectToRoute('logout');
                    } else {
                        return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
                    }
                } else {
                    if ($this->isEmailInUse($editUserForm->getData()['email']) && $this->isEmailCorrect($editUserForm->getData()['email'])) {
                        $user->setEmail($editUserForm->getData()['email']);
                        $activationToken = hash("sha256", $editUserForm->getData()['email']);
                        $user->setActivationToken($activationToken);
                        $user->setEmailActivate(0);
                        $this->getDoctrine()->getManager()->flush();

                        $this->sendEmail($user, $mailer);

                        return $this->redirectToRoute('logout');
                    }
                    return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
                }
            }

        } elseif ($loggedUserRole === 'moderator' && strval($loggedUserId) !== $id) {

            $em = $this->getDoctrine()->getManager();
            $city = $this->getUser()->getCity()->getId();
            $cinemas = $em->getRepository('AppBundle:Cinema')->findBy(array('city' => $city));
            $users = $em->getRepository('AppBundle:User')->findBy(array('cinema' => $cinemas));
            $ids = array();

            foreach ($users as $userId) {
                $ids[] = strval($userId->getId());
            }

            if (!in_array($id, $ids)) return $this->redirectToRoute('user_index');

            $editForm = $this->createForm('AppBundle\Form\UserUpdateModeratorType', $user);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
            }

        } else {

            $editForm = $this->createForm('AppBundle\Form\UserUpdateAdminType', $user);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {

                $passwordHash = password_hash($editForm->getData()->getPassword(), PASSWORD_BCRYPT);
                $user->setPassword($passwordHash);
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
            }
        }
        return $this->render('user/edit.html.twig', array(
            'loggedUserRole' => $loggedUserRole,
            'user' => $user,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a user entity.
     *
     * @Route("user/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, User $user)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser();
        $hasAccess = $this->hasAccessAd($loggedUserRole);

        if(!$hasAccess) return $this->redirectToRoute('homepage');

        $form = $this->createDeleteForm($user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * Creates a form to delete a user entity.
     *
     * @param User $user The user entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(User $user)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('user_delete', array('id' => $user->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Verify account
     *
     * @Route("/verify", name="verify")
     */
    public function verifyAction(Request $request)
    {
        $token = $request->query->get('activationToken');
        if($this->activationTokenCorrect($token)) {
            $email = $this->changeEmailActivateTrue($token);
            return $this->render('login/login.html.twig', array(
                'errors' => '',
                'email' => $email,
            ));
        }
    }

    /**
     * Remind password
     *
     * @Route("/remindPassword", name="remind_password")
     */
    public function remindPassword(Request $request, \Swift_Mailer $mailer)
    {

        $form = $this->createForm('AppBundle\Form\RemindPasswordType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->getData()['email'];

            if (!$this->isEmailInUse($email)) {
                $em = $this->getDoctrine()->getManager();
                $user = $em->getRepository('AppBundle:User')->findOneBy(array('email' => $email));
                $newPassword = $this->generatePassword();
                $passwordHash = password_hash($newPassword, PASSWORD_BCRYPT);
                $this->updatePassword($user->getId(), $passwordHash);

                $message = (new \Swift_Message('New password'))
                    ->setFrom('ticketmaniac2018@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody(
                        $this->renderView(
                            'Emails/password.html.twig', array(
                                'name' => $user->getName(),
                                'password' => $newPassword,
                            )
                        ),
                        'text/html'
                    );

                $mailer->send($message);

                return $this->redirectToRoute('homepage');
            }
        }
        return $this->render('remind_password/remind_password.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @param $email
     * @return bool
     */
    public function isEmailInUse($email)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(array('email' => $email));
        if(empty($user)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $email
     * @return bool
     */
    public function isEmailCorrect($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $password
     * @return bool
     */
    public function isOldPasswordCorrect($password)
    {
        $loggedUserPassword = $this->get('security.token_storage')->getToken()->getUser()->getPassword();
        if(password_verify($password, $loggedUserPassword)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $token
     * @return bool
     */
    public function activationTokenCorrect($token)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(array('activationToken' => $token));

        if(empty($user)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * @param $token
     * @return mixed
     */
    public function changeEmailActivateTrue($token)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(array('activationToken' => $token));
        $user->setEmailActivate(1);
        $this->getDoctrine()->getManager()->flush();
        return $user->getEmail();
    }

    /**
     * @param $token
     * @return mixed
     */
    public function changeEmailActivateFalse($token)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(array('activationToken' => $token));
        $user->setEmailActivate(1);
        $this->getDoctrine()->getManager()->flush();
        return $user->getEmail();
    }

    /**
     * @return bool|string
     */
    public function generatePassword()
    {
        $rand = substr(md5(microtime()),rand(0,26),8);
        return $rand;
    }

    /**
     * @param $id
     * @param $password
     */
    public function updatePassword($id, $password)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(array('id' => $id));
        $user->setPassword($password);
        $this->getDoctrine()->getManager()->flush();
    }

    private function hasAccess($token)
    {
        if($token !== 'anon.') {
            return true;
        } else {
            return false;
        }
    }

    private function hasAccessAdMod($loggedUserRole)
    {
        if($loggedUserRole !== 'anon.') {
            if($loggedUserRole->getRole() === 'admin' || $loggedUserRole->getRole() === 'moderator') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function hasAccessAd($loggedUserRole)
    {
        if($loggedUserRole !== 'anon.') {
            if($loggedUserRole->getRole() === 'admin') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function sendEmail($user, \Swift_Mailer $mailer)
    {
        $data = [
            'name' => $user->getName(),
            'activationToken' => $user->getActivationToken()
        ];

        $message = (new \Swift_Message('Registration'))
            ->setFrom('ticketmaniac2018@gmail.com')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderView(
                    'Emails/register.html.twig', $data),
                'text/html'
            );

        $mailer->send($message);
    }
}
