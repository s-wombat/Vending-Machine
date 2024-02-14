<?php

namespace App\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

#[AsCommand(name: 'app:vending-machine')]
class VendingMachineCommand extends Command
{
    protected function configure(): void
    {
        parent::configure();
        $this
            ->setDescription('Розрахувати здачу за покупку товару')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $products = [
            "Coca-cola" => 1.50,
            "Snickers" => 1.20,
            "Lay's" => 2.00,
        ];

        // Список товарів та цін
        $output->writeln('Список товарів та цін:');
        foreach ($products as $product => $price) {
            $output->writeln("$product $price");
        }

        // Виберіть продукт
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Виберіть продукт: ',
            array_keys($products)
        );
        $question->setErrorMessage('Такого товару немає.');
        $selectedProduct = $helper->ask($input, $output, $question);
        $output->writeln('Вы выбрали: '.$selectedProduct);
        $selectedPrice = $products[$selectedProduct];

        // Оплата товару
        $coins = [0.01, 0.05, 0.10, 0.25, 0.50, 1.00];
        $totalCoins = 0;
        $output->writeln('Приймаємо монети наступних номіналів: 0.01, 0.05, 0.10, 0.25, 0.50, 1.00');
        do {
            $question = new Question('Введіть номінал монети (для закінчення введіть "Enter"): ');
            $coin = $helper->ask($input, $output, $question);

            if ($coin === 0 || !in_array($coin, $coins)) {
                $output->writeln('Неприпустима монета. Спробуйте ще раз.');
                continue;
            }

            $totalCoins += $coin;
            $output->writeln("Загальна сума: $totalCoins.");

            if ($totalCoins < $selectedPrice) {
                $need = $selectedPrice - $totalCoins;
                $output->writeln("Не вистачає: $need");
            }

            if ($totalCoins >= $selectedPrice) {
                $change = $totalCoins - $selectedPrice;

                $output->writeln("Вы купили $selectedProduct. Ваша здача: $change");
                break;
            }
        } while (true);

        return Command::SUCCESS;
    }
}