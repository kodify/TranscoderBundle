<?php
namespace Kodify\TranscoderBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Kodify\TranscoderBundle\Service\Driver\PandaStream;
use Kodify\AdminBundle\Entity\Video;
use Kodify\AdminBundle\Entity\PutIoFile;

class TranscoderApiCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $availableCommands = array(
            'getVideosList' => 'page number',
            'getVideosListByStatus' => 'status',
            'getVideoById' => 'video id',
            'getVideoEncodingsById' => 'video id',
            'getVideoMetadataById' => 'video id',
            'getVideoCompleteDataById' => 'video id',
            'deleteVideoById' => 'video id',
            'getClipsList' => null,
            'getClipsListByStatus' => 'status',
            'getClipById' => 'job id',
            'getClipsListByVideoId' => 'video id',
            'cancelClipEncodingById' => 'job id',
            'deleteClipById' => 'job id',
            'getFormatsList' => null,
            'getFormatById' => 'format id',
            'deleteFormatById' => 'format id',
        );

        $helpStr = '';
        foreach ($availableCommands as $command => $param) {
            $helpStr  .= '      '.$command . '(' . $param . ")\n";
        }

        $this
            ->setName('kodify:transcoder:api')
            ->setDescription('Do requests to transcoder api')
            ->setHelp("Available commands:\n" . $helpStr)
            ->addArgument(
                'operation',
                InputArgument::REQUIRED,
                'Command to call'
            )
            ->addArgument(
                'parameter',
                InputArgument::OPTIONAL,
                'Command parameter'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $transcoder = $this->getContainer()->get('kodify_transcoder');

        $method = $input->getArgument('operation');
        $param = $input->getArgument('parameter');

        $this->getContainer()->get('logger')->info('msg="manual trasncoder api call" command="' . $method . '" param="' . $param . '"');

        $output->writeln('<info>method="'.$method.'" param="'.$param.'"</info>');

        $response = $transcoder->$method($param);
        $output->writeln('<info>$response:</info>');
        $output->writeln(print_r($response, true));

    }
}
