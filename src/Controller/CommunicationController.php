<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\CommunicationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/communication', name: 'app_communication_')]
#[IsGranted('ROLE_USER')]
class CommunicationController extends AbstractController
{
    public function __construct(
        private CommunicationService $communicationService
    ) {}

    #[Route('/notifications', name: 'notifications')]
    public function notifications(): Response
    {
        $user = $this->getUser();
        
        // Récupérer toutes les notifications
        $notifications = $this->communicationService->getUserNotifications($user);
        
        return $this->render('communication/notifications.html.twig', [
            'notifications' => $notifications,
            'user' => $user,
        ]);
    }

    #[Route('/messages', name: 'messages')]
    public function messages(): Response
    {
        $user = $this->getUser();
        
        // Récupérer tous les messages
        $messages = $this->communicationService->getUserMessages($user);
        
        return $this->render('communication/messages.html.twig', [
            'messages' => $messages,
            'user' => $user,
        ]);
    }

    #[Route('/alerts', name: 'alerts')]
    public function alerts(): Response
    {
        $user = $this->getUser();
        
        // Récupérer toutes les alertes
        $alerts = $this->communicationService->getUserAlerts($user);
        
        return $this->render('communication/alerts.html.twig', [
            'alerts' => $alerts,
            'user' => $user,
        ]);
    }

    #[Route('/mark-notification-read/{id}', name: 'mark_notification_read', methods: ['POST'])]
    public function markNotificationRead(int $id): Response
    {
        $user = $this->getUser();
        
        $this->communicationService->markNotificationAsRead($user, $id);
        
        return $this->json(['success' => true]);
    }

    #[Route('/mark-all-notifications-read', name: 'mark_all_notifications_read', methods: ['POST'])]
    public function markAllNotificationsRead(): Response
    {
        $user = $this->getUser();
        
        $this->communicationService->markAllNotificationsAsRead($user);
        
        return $this->json(['success' => true]);
    }

    #[Route('/settings', name: 'settings')]
    public function settings(Request $request): Response
    {
        $user = $this->getUser();
        
        if ($request->isMethod('POST')) {
            $preferences = $request->request->all();
            $this->communicationService->updateUserPreferences($user, $preferences);
            
            $this->addFlash('success', 'Préférences de communication mises à jour avec succès !');
            return $this->redirectToRoute('app_communication_settings');
        }
        
        $preferences = $this->communicationService->getUserPreferences($user);
        
        return $this->render('communication/settings.html.twig', [
            'preferences' => $preferences,
            'user' => $user,
        ]);
    }
}
