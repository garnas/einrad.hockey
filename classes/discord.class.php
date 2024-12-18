<?php

class Discord
{

    private static string $webhook = Env::WEBHOOK_DISCORD;

    private static function body(string $message): false|string
    {
        try {
            return json_encode([
                "content" => $message,
                "username" => "DieFlotteSpeiche",
                "avatar_url" => "https://einrad.hockey/bilder/logo_kurz_small.png",
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        } catch (JsonException $exception) {
            Helper::log("discord.log", $exception->getMessage() . "/r/n" . $exception->getTraceAsString());
            return false;
        }
    }

    public static function tickerUpdate($spiel): void
    {
        if (!empty($spiel['penalty_a']) ||  !empty($spiel['penalty_b'])) {
            $message =  "Penalty!\r\n"
                . $spiel['teamname_a'] . " : " . $spiel['teamname_b'] . " " . $spiel['penalty_a'] . " : " . $spiel['penalty_b'];
        } else {
            $message = $spiel['teamname_a'] . " : " . $spiel['teamname_b'] . " " . $spiel['tore_a'] . " : " . $spiel['tore_b'] ;
        }
        self::send($message);
    }

    public static function send($message): void
    {

        if (Env::IS_LOCALHOST) {
            Html::info("Discord:\r\n" . $message);
            return;
        }

        $body = self::body($message);

        // JsonEncode Error
        if ($body === false){
            return;
        }

        $send = curl_init(self::$webhook);
        curl_setopt($send, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($send, CURLOPT_POST, 1);
        curl_setopt($send, CURLOPT_POSTFIELDS, $body);
        curl_setopt($send, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($send, CURLOPT_HEADER, 0);
        curl_setopt($send, CURLOPT_RETURNTRANSFER, 1);


        $output = curl_exec($send);

        if ($output === false) {
            Helper::log("discord.log", "Error:/r/n" . self::$webhook . "/r/n" . $body);
        }

        if(!empty($output)) {
            Helper::log("discord.log", "Error:/r/n" . $output);
        }

        curl_close($send);

    }

    public static function send_with_turnier(string $message, nTurnier $turnier): void {
            $message = "**"
                . $turnier->get_datum()
                . " " . $turnier->get_ort()
                . " (" . $turnier->get_tblock() . ")"
                . "**"
                . " - *" . Helper::get_akteur() . "*"
                . "\r\n"
                . $message;
           // self::send($message);
    }

}