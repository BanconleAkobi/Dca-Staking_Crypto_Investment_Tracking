<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Notification;
use App\Entity\Message;
use App\Entity\Alert;
use App\Repository\NotificationRepository;
use App\Repository\MessageRepository;
use App\Repository\AlertRepository;
use Doctrine\ORM\EntityManagerInterface;

class CommunicationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private NotificationRepository $notificationRepository,
        private MessageRepository $messageRepository,
        private AlertRepository $alertRepository
    ) {}

    public function getUserNotifications(User $user): array
    {
        $notifications = $this->notificationRepository->findByUser($user);
        
        // Si aucune notification, créer quelques exemples
        if (empty($notifications)) {
            $this->createSampleNotifications($user);
            $notifications = $this->notificationRepository->findByUser($user);
        }
        
        return $notifications;
    }

    public function getUserMessages(User $user): array
    {
        $messages = $this->messageRepository->findByUser($user);
        
        // Si aucun message, créer quelques exemples
        if (empty($messages)) {
            $this->createSampleMessages($user);
            $messages = $this->messageRepository->findByUser($user);
        }
        
        return $messages;
    }

    public function getUserAlerts(User $user): array
    {
        $alerts = $this->alertRepository->findActiveByUser($user);
        
        // Si aucune alerte, créer quelques exemples
        if (empty($alerts)) {
            $this->createSampleAlerts($user);
            $alerts = $this->alertRepository->findActiveByUser($user);
        }
        
        return $alerts;
    }

    public function markNotificationAsRead(User $user, int $notificationId): void
    {
        $notification = $this->notificationRepository->find($notificationId);
        if ($notification && $notification->getUser() === $user) {
            $notification->markAsRead();
            $this->entityManager->flush();
        }
    }

    public function markAllNotificationsAsRead(User $user): void
    {
        $this->notificationRepository->markAllAsReadByUser($user);
    }

    public function getUserPreferences(User $user): array
    {
        // Simuler les préférences utilisateur
        return [
            'email_notifications' => true,
            'sms_notifications' => false,
            'push_notifications' => true,
            'payment_alerts' => true,
            'performance_alerts' => true,
            'assembly_reminders' => true,
            'document_notifications' => true,
            'system_updates' => false,
            'marketing_emails' => false,
            'frequency' => 'immediate', // immediate, daily, weekly
        ];
    }

    public function updateUserPreferences(User $user, array $preferences): void
    {
        // Dans un vrai système, sauvegarder les préférences en base de données
        // Pour l'instant, on simule juste
    }

    public function sendNotification(User $user, string $type, string $title, string $message, array $options = []): void
    {
        // Dans un vrai système, envoyer une notification à l'utilisateur
        // Ceci pourrait inclure email, SMS, push notification, etc.
    }

    public function sendBulkNotification(array $users, string $type, string $title, string $message, array $options = []): void
    {
        // Dans un vrai système, envoyer une notification à plusieurs utilisateurs
        foreach ($users as $user) {
            $this->sendNotification($user, $type, $title, $message, $options);
        }
    }

    public function createPriceAlert(User $user, string $investment, float $threshold, string $condition = 'above'): void
    {
        // Dans un vrai système, créer une alerte de prix
    }

    public function createPaymentReminder(User $user, string $investment, float $amount, \DateTime $dueDate): void
    {
        // Dans un vrai système, créer un rappel de paiement
    }

    public function createAssemblyReminder(User $user, string $company, \DateTime $assemblyDate): void
    {
        // Dans un vrai système, créer un rappel d'assemblée générale
    }

    private function createSampleNotifications(User $user): void
    {
        $notifications = [
            [
                'type' => 'payment',
                'title' => 'Paiement reçu',
                'message' => 'Vous avez reçu un paiement de $150.00 de TechCorp Inc.',
                'priority' => 'high',
                'icon' => 'fas fa-money-bill-wave',
                'color' => 'success',
                'createdAt' => new \DateTime('-2 hours'),
            ],
            [
                'type' => 'assembly',
                'title' => 'Assemblée générale',
                'message' => 'L\'assemblée générale de GreenEnergy Ltd. aura lieu le 15/12/2024',
                'priority' => 'medium',
                'icon' => 'fas fa-users',
                'color' => 'info',
                'createdAt' => new \DateTime('-1 day'),
            ],
            [
                'type' => 'document',
                'title' => 'Nouveau document',
                'message' => 'Un nouveau rapport semestriel est disponible pour TechCorp Inc.',
                'priority' => 'low',
                'icon' => 'fas fa-file-alt',
                'color' => 'primary',
                'createdAt' => new \DateTime('-3 days'),
                'readAt' => new \DateTime('-2 days'),
            ],
            [
                'type' => 'alert',
                'title' => 'Alerte de performance',
                'message' => 'Votre investissement dans CryptoCorp a chuté de 15% cette semaine',
                'priority' => 'high',
                'icon' => 'fas fa-exclamation-triangle',
                'color' => 'warning',
                'createdAt' => new \DateTime('-5 days'),
                'readAt' => new \DateTime('-4 days'),
            ],
            [
                'type' => 'system',
                'title' => 'Maintenance programmée',
                'message' => 'Une maintenance est prévue le 20/12/2024 de 2h à 4h',
                'priority' => 'low',
                'icon' => 'fas fa-tools',
                'color' => 'secondary',
                'createdAt' => new \DateTime('-1 week'),
                'readAt' => new \DateTime('-6 days'),
            ],
        ];

        foreach ($notifications as $data) {
            $notification = new Notification();
            $notification->setUser($user);
            $notification->setType($data['type']);
            $notification->setTitle($data['title']);
            $notification->setMessage($data['message']);
            $notification->setPriority($data['priority']);
            $notification->setIcon($data['icon']);
            $notification->setColor($data['color']);
            $notification->setCreatedAt($data['createdAt']);
            
            if (isset($data['readAt'])) {
                $notification->setReadAt($data['readAt']);
            }

            $this->entityManager->persist($notification);
        }

        $this->entityManager->flush();
    }

    private function createSampleMessages(User $user): void
    {
        $messages = [
            [
                'fromSender' => 'Support Crypto Investment Tracker',
                'subject' => 'Bienvenue sur notre plateforme !',
                'content' => 'Merci de vous être inscrit sur notre plateforme. Nous sommes ravis de vous accompagner dans la gestion de vos investissements.',
                'type' => 'welcome',
                'createdAt' => new \DateTime('-1 week'),
                'readAt' => new \DateTime('-6 days'),
            ],
            [
                'fromSender' => 'TechCorp Inc.',
                'subject' => 'Rapport trimestriel disponible',
                'content' => 'Notre rapport trimestriel est maintenant disponible. Vous pouvez le consulter dans votre espace investisseur.',
                'type' => 'company',
                'createdAt' => new \DateTime('-3 days'),
            ],
            [
                'fromSender' => 'GreenEnergy Ltd.',
                'subject' => 'Invitation à l\'assemblée générale',
                'content' => 'Vous êtes invité à participer à notre assemblée générale annuelle qui se tiendra le 15/12/2024 à 14h.',
                'type' => 'company',
                'createdAt' => new \DateTime('-5 days'),
            ],
            [
                'fromSender' => 'Support Crypto Investment Tracker',
                'subject' => 'Nouvelles fonctionnalités disponibles',
                'content' => 'Découvrez les nouvelles fonctionnalités de notre plateforme : calendrier des échéances, assemblées générales, et bien plus !',
                'type' => 'system',
                'createdAt' => new \DateTime('-1 week'),
                'readAt' => new \DateTime('-5 days'),
            ],
        ];

        foreach ($messages as $data) {
            $message = new Message();
            $message->setUser($user);
            $message->setFromSender($data['fromSender']);
            $message->setSubject($data['subject']);
            $message->setContent($data['content']);
            $message->setType($data['type']);
            $message->setCreatedAt($data['createdAt']);
            
            if (isset($data['readAt'])) {
                $message->setReadAt($data['readAt']);
            }

            $this->entityManager->persist($message);
        }

        $this->entityManager->flush();
    }

    private function createSampleAlerts(User $user): void
    {
        $alerts = [
            [
                'type' => 'price_alert',
                'title' => 'Alerte de prix - Bitcoin',
                'message' => 'Le prix du Bitcoin a atteint $65,000',
                'threshold' => '65000',
                'condition' => 'above',
                'createdAt' => new \DateTime('-1 hour'),
            ],
            [
                'type' => 'payment_reminder',
                'title' => 'Rappel de paiement',
                'message' => 'Un paiement de $75.50 est attendu de GreenEnergy Ltd. dans 3 jours',
                'dueDate' => new \DateTime('+3 days'),
                'createdAt' => new \DateTime('-2 hours'),
            ],
            [
                'type' => 'performance_alert',
                'title' => 'Performance négative',
                'message' => 'Votre investissement dans CryptoCorp a perdu 15% cette semaine',
                'createdAt' => new \DateTime('-1 day'),
            ],
            [
                'type' => 'assembly_reminder',
                'title' => 'Assemblée générale',
                'message' => 'L\'assemblée générale de TechCorp Inc. aura lieu dans 2 jours',
                'dueDate' => new \DateTime('+2 days'),
                'createdAt' => new \DateTime('-3 hours'),
            ],
        ];

        foreach ($alerts as $data) {
            $alert = new Alert();
            $alert->setUser($user);
            $alert->setType($data['type']);
            $alert->setTitle($data['title']);
            $alert->setMessage($data['message']);
            $alert->setCreatedAt($data['createdAt']);
            
            if (isset($data['threshold'])) {
                $alert->setThreshold($data['threshold']);
            }
            
            if (isset($data['condition'])) {
                $alert->setCondition($data['condition']);
            }
            
            if (isset($data['dueDate'])) {
                $alert->setDueDate($data['dueDate']);
            }

            $this->entityManager->persist($alert);
        }

        $this->entityManager->flush();
    }
}
