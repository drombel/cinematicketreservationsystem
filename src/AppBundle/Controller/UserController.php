<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
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
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser()->getRole();

        if($loggedUserRole === 'admin') {
            $em = $this->getDoctrine()->getManager();
            $users = $em->getRepository('AppBundle:User')->findAll();

            return $this->render('user/index.html.twig', array(
                'users' => $users,
            ));
        } else {
            return $this->redirect('http://localhost:8080/cinematicketreservationsystem/web/app_dev.php');
        }

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

            /*$message = (new \Swift_Message('Registration'))
                ->setFrom('test@example.com')
                ->setTo($user->getEmail())
                ->setBody(
                    $this->renderView(
                        'Emails/register.html.twig', array(
                            'name' => $user->getName()
                        )
                    ),
                    'text/html'
                );

            $mailer->send($message);*/

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
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser()->getRole();

        if($loggedUserRole === 'admin') {
            $deleteForm = $this->createDeleteForm($user);
            return $this->render('user/show.html.twig', array(
                'user' => $user,
                'delete_form' => $deleteForm->createView(),
            ));
        } else {
            return $this->redirect('http://localhost:8080/cinematicketreservationsystem/web/app_dev.php');
        }
    }

    /**
     * Displays a form to edit an existing user entity.
     *
     * @Route("user/{id}/edit", name="user_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, User $user, UserPasswordEncoderInterface $passwordEncoder, $id)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser()->getRole();
        $loggedUserId = $this->get('security.token_storage')->getToken()->getUser()->getId();

        $deleteForm = $this->createDeleteForm($user);

        if($loggedUserRole === 'client' && strval($loggedUserId) === $id) {

            $editUserForm = $this->createForm('AppBundle\Form\UserUpdateType');
            $editUserForm->handleRequest($request);

            if ($editUserForm->isSubmitted() && $editUserForm->isValid()) {
                if($editUserForm->getData()['email'] == "") {
                    if($this->isOldPasswordCorrect($editUserForm->getData()['oldPassword'])) {
                        $passwordHash = password_hash($editUserForm->getData()['newPassword'],PASSWORD_BCRYPT);
                        $user->setPassword($passwordHash);
                        $this->getDoctrine()->getManager()->flush();
                        return $this->redirectToRoute('logout');
                    } else {
                        return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
                    }
                } else {
                    if($this->isEmailInUse($editUserForm->getData()['email']) && $this->isEmailCorrect($editUserForm->getData()['email'])) {
                        $user->setEmail($editUserForm->getData()['email']);
                        $activationToken = hash("sha256", $editUserForm->getData()['email']);
                        $user->setActivationToken($activationToken);
                        $user->setEmailActivate(0);
                        $this->getDoctrine()->getManager()->flush();
                        //WYSLAC MEJLA AKTYWACYJNEGO
                        return $this->redirectToRoute('logout');
                    }
                    return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
                }
            }

            return $this->render('user/edit.html.twig', array(
                'loggedUserRole' => $loggedUserRole,
                'user' => $user,
                'edit_form' => $editUserForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ));

        } elseif($loggedUserRole === 'client' && strval($loggedUserId) !== $id) {

            return $this->redirect('http://localhost:8080/cinematicketreservationsystem/web/app_dev.php');

        } else {

            $editForm = $this->createForm('AppBundle\Form\UserUpdateAdminType', $user);
            $editForm->handleRequest($request);

            if ($editForm->isSubmitted() && $editForm->isValid()) {
                $passwordHash = password_hash($editForm->getData()['newPassword'],PASSWORD_BCRYPT);
                $user->setPassword($passwordHash);
                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('user_edit', array('id' => $user->getId()));
            }

            return $this->render('user/edit.html.twig', array(
                'loggedUserRole' => $loggedUserRole,
                'user' => $user,
                'edit_form' => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
            ));
        }
    }

    /**
     * Deletes a user entity.
     *
     * @Route("user/{id}", name="user_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, User $user)
    {
        $loggedUserRole = $this->get('security.token_storage')->getToken()->getUser()->getRole();

        if($loggedUserRole === 'admin') {
            $form = $this->createDeleteForm($user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->remove($user);
                $em->flush();
            }

            return $this->redirectToRoute('user_index');
        } else {
            return $this->redirect('http://localhost:8080/cinematicketreservationsystem/web/app_dev.php');
        }
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

    public function isEmailActive($email)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->findOneBy(array('email' => $email));

        if(empty($user) || ($user->getEmailActivate() == 0)) {
            return false;
        } else {
            return true;
        }
    }

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

    public function isEmailCorrect($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return true;
        } else {
            return false;
        }
    }

    public function isOldPasswordCorrect($password)
    {
        $loggedUserPassword = $this->get('security.token_storage')->getToken()->getUser()->getPassword();
        if(password_verify($password, $loggedUserPassword)) {
            return true;
        } else {
            return false;
        }
    }
}
