<?php
namespace AppBundle\Command;

use AppBundle\Controller\ChronosController;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use \DateTime;

class GreetCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('cron:remove_seats')
            ->setDescription('Removing unused seats');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $doc = $this->getContainer()->get('doctrine');
        $repoTicket = $doc->getRepository("AppBundle:Ticket");

        while(true){
            $now = (new DateTime());
            foreach ($repoTicket->findBy(array('status' => 'Pending')) as $ticket){
                $date = $ticket->getSendMailDate();
                $date->modify('+30 minutes');
                if ($now > $date){
                    $repoTicket->deleteSeatsById($ticket->getId());
                    echo "usunalem ticket o id = ".$ticket->getId();
                }
            }
            echo ($now->format('H:i')."\n");
            sleep(60);  // once every minute
        }
    }
}