<?php

/*
 * MailWatch for MailScanner
 * Copyright (C) 2003-2011  Steve Freegard (steve@freegard.name)
 * Copyright (C) 2011  Garrod Alwood (garrod.alwood@lorodoes.com)
 * Copyright (C) 2014-2018  MailWatch Team (https://github.com/mailwatch/1.2.0/graphs/contributors)
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
 *
 * In addition, as a special exception, the copyright holder gives permission to link the code of this program with
 * those files in the PEAR library that are licensed under the PHP License (or with modified versions of those files
 * that use the same license as those files), and distribute linked combinations including the two.
 * You must obey the GNU General Public License in all respects for all of the code used other than those files in the
 * PEAR library that are licensed under the PHP License. If you modify this program, you may extend this exception to
 * your version of the program, but you are not obligated to do so.
 * If you do not wish to do so, delete this exception statement from your version.
 *
 * You should have received a copy of the GNU General Public License along with this program; if not, write to the Free
 * Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

namespace MailWatch\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use MailWatch\Entity\User;

class PasswordResetController extends Controller
{
    /**
     * @Route("/password-reset", name="password-reset")
     */
    public function passwordReset(Request $request)
    {
        //required for localization
        $compatSrc = $this->get('kernel')->getProjectDir() . '/mailscanner/';
        if (!is_readable($compatSrc . 'conf.php')) {
            throw new \Exception(\MailWatch\Translation::__('cannot_read_conf'));
        }
        require_once $compatSrc . 'conf.php';
        require_once $compatSrc . 'functions.php';

        if (!defined('PWD_RESET') || false === PWD_RESET) {
            throw new \Exception(\MailWatch\Translation::__('conferror63'));
        }

        if (defined('USE_LDAP') && true === USE_LDAP) {
            throw new \Exception(\MailWatch\Translation::__('pwdresetldap63'));
        }

        /*$repo = $this->getDoctrine()->getRepository(User::class);
        $user = $repo->findOneBy(
            array('username' => 'asuweb')
        );*/

        //Stage 1 - we need to get the email address
        $renderParams = array();
        $renderParams['pagetitle'] = \MailWatch\Translation::__('title63');
        if ($request->query->get('stage') === '1') {
            $username = array();
            $form = $this->createFormBuilder($username)
                ->add('email', EmailType::class, array(
                    'label' => \MailWatch\Translation::__('emailaddress63'),
                    'required' => true,
                    'constraints' => array(new Assert\Email())
                ))
                ->add('save', SubmitType::class, array('label' => \MailWatch\Translation::__('requestpwdreset63')))
                ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //check if user exists and if so, send password reset link

                $renderParams['action'] = 'success';
                $renderParams['message'] = 'Form Submitted';
            }
            elseif ($form->isSubmitted() && !$form->isValid()) {
                $renderParams['action'] = 'error';
                $renderParams['message'] = 'Form Validation Failed';
                $renderParams['form'] = $form->getErrors();
            }
            else {
                $renderParams['action'] = 'form';
                $renderParams['form'] = $form->createView();
            }
        } else {
            $renderParams['action'] = 'none';
            $renderParams['message'] = 'Nothing to do';
        }
        return $this->render('Security/password-reset-test.html.twig', $renderParams);
    }
}