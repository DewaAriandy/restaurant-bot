<?php
 
namespace App\Conversations;
 
use Illuminate\Foundation\Inspiring;
use BotMan\BotMan\Messages\Incoming\Answer;
use BotMan\BotMan\Messages\Outgoing\Question;
use BotMan\BotMan\Messages\Outgoing\Actions\Button;
use BotMan\BotMan\Messages\Conversations\Conversation;
use BotMan\BotMan\Messages\Outgoing\Question as BotManQuestion;
use BotMan\BotMan\Messages\Incoming\Answer as BotManAnswer;
 
 
class ExampleConversation extends Conversation
{
    /**
     * First question
     */
    public function askRestaurant()
    {
        $question = Question::create("Silahkan pilih Restaurant yang ingin dicari.")
            ->fallback('Unable to ask question')
            ->callbackId('ask_reason')
            ->addButtons([
                Button::create('Restaurant A')->value('A'),
                Button::create('Restaurant B')->value('B'),
                Button::create('Restaurant C')->value('C'),
            ]);
 
        return $this->ask($question, function (Answer $answer) {
            if ($answer->isInteractiveMessageReply()) {
                switch ($answer->getValue()) {
                    case 'A':
                        $this->restaurant = 'A';
                        $this->jawabanNya('Restaurant A');
                        break;
                    case 'B':
                        $this->restaurant = 'B';
                        $this->jawabanNya('Restaurant B');
                        break;
                    case 'C':
                        $this->restaurant = 'C';
                        $this->jawabanNya('Restaurant C');
                        break; 
                    default:
                        # code...
                       break;
                }
 
            }
        });
    }
 
    public function jawabanNya($restaurant)
    {
        // $this->ask('Kamu memilih kitab dari '.$tokoh.'. Silahkan masukkan nomor hadits yang ingin dicari.', function (Answer $answer) {
        //     $no = $answer->getText();
        //     // echo $no;
        //     $hasil=$this->getData($this->kitab, $no);
        //     $jawaban = sprintf("Hadits menjelaskan tentang: ".$hasil[3].". \r\t\n\n ".$hasil[0]."\r\t\n\n ".$hasil[1]);
        //     // $this->say('Hadits menjelaskan tentang: '.$hasil[3]);
        //     // $this->say($hasil[0]);
        //     $this->say($jawaban);
        //     if ($hasil[2]==true) {
        //         $this->say('Silahkan laporkan permasalahan ini dengan menu /lapor .');
        //     }
        // });

        $this->ask('Kamu memilih '.$restaurant, function (Answer $answer) {
            
        });
    }
 
    public function getData($kitab, $no)
    {
        try {
            $str='https://scrape-fastapi.herokuapp.com/hadits/?tokoh='.$kitab.'&no='.$no;
            // $str='https://hadits-api-zhirrr.vercel.app/books/'.$kitab.'/'.$no;
            $dt = json_decode(file_get_contents($str));
            return [$dt->data->contents->arab, $dt->data->contents->id, false, $dt->data->contents->judul];
        } catch (\Throwable $th) {
            return ["Something went wrong 😯️","Sepertinya ada masalah.🧐️", true];
        }
 
    }
    /**
     * Start the conversation
     */
    public function run()
    {
        $this->askRestaurant();
    }
}