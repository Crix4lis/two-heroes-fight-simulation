> author: Michał Powała <br>
> source repository: [two-heroes-fight-simulation](https://github.com/Crix4lis/two-heroes-fight-simulation)

# two-heroes-fight-simulation
This repository contains solution(s) to given task (below in this file) approached in different ways.
Each approach (that is finished) has its own branch and own tag. Application is runnable form cli.<br><br>
Purpose of this repository is (for me) to practise clean code and a bit of good code design, which follow:

1. Four pillars of OOP (for me if you focus on SOLID they came up themself)
    - Abstraction
    - Inheritance
    - Encapsulation
    - Polymorphism
1. SOLID Principles
    - Single responsibility (each of my class has its own unique task)
    - Open/closed (when I starded to play with different approaches most of my internals were untouched,
    just needed to add new functionality)
    - Liskov substitution principle (my code uses abstraction where needed and is unaware of underlying implementation;
    look at Unit and Colleague classes for example)
    - Interface segregation principle (many small interfaces instead of one big)
    - Dependency inversion principle (the only place where classes are instantiated is run.php script and factories,
    and look at Randomizer - it can be mocked! I don't use rand() directly in any class)
1. Design Patterns
    - Decorator (src/Modifier)
    - Factory Method
    - Observer Pattern (proper branch)
    - Mediator Pattern (proper branch)
1. DDD tactical patterns
    - Value Object (src/Property)
    - Aggregate (src/Unit - every bit of logic that could be encapsulated here is here)
    - Domain Service (TurnService)
    - Application Service (GamePlayService)
    - Factories (factories)
    - Events (src/Event) (yeah, I could use event bus instead)
1. Loose coupling (it arises from SOLID)<br>
Action/Event loggers, error loggers, printer (reader),
and core logic are all loosely coupled with each other
1. TDD<br>
To be honest it's not pure TDD where you first implement tests which fail and then source code after which 
tests pass (I think it's not practical) but after I created some abstraction level or a class I tested it right away
and fixed all minor bugs if needed or logical errors that I hadn't thought over. That's why when i implemented
run.php script (which is entry point to application) and I did it as the last step, the code worked!
I think that's good enough and pure TDD is not needed.

## How to run
1. Run docer: `docker-compose up -d`
    - run application: `docker-compose exec cli php run.php`
    - run unit tests `docker-compose exec cli vendor/bin/phpunit tests/`

## TASK
Create battle simulation between Orderus and beast. Each time when battle starts, beast and Orderus
are generated with different statistics that follow following rules:
- Orderus:
    - Health: 70 - 100
    - Strength: 70 - 80
    - Defence: 45 – 55
    - Speed: 40 – 50
    - Luck: 10% - 30% (0% means no luck, 100% lucky all the time)
    - additional skills:
        - Rapid strike: Strike twice while it’s his turn to attack; there’s a 10% chance
        he’ll use this skill every time he attacks
        - Magic shield: Takes only half of the usual damage when an enemy attacks;
        there’s a 20% change he’ll use this skill every time he defends
- Beast:
    - Health: 60 - 90
    - Strength: 60 - 90
    - Defence: 40 – 60
    - Speed: 40 – 60
    - Luck: 25% - 40%

Gameplay rules:
- First attack is done by the player with the higher speed. If both players have
   the same speed, than the attack is carried on by the player with the highest luck.
- After an attack, the players switch roles: the attacker now defends and the
   defender now attacks.
- The damage done by the attacker is calculated with the following formula:
   `Damage = Attacker strength – Defender defence`
- The damage is subtracted from the defender’s health. An attacker can miss their
hit and do no damage if the defender gets lucky that turn.
- Orderus’ skills occur randomly, based on their chances, so take them into
account on each turn.
- Game ends when one of the players remain without health or the number of
turns reaches 20.
- The application must output the results each turn: what
happened, which skills were used (if any), the damage done, defender’s health
left.
- If we have a winner before the maximum number of rounds is reached, he must
be declared.

## BRANCHES and TAGS
 - branch: [base](https://github.com/Crix4lis/two-heroes-fight-simulation/tree/base); tag: [base-running-app](https://github.com/Crix4lis/two-heroes-fight-simulation/tree/base-running-app):<br>
 *Contains base solution (core functionality, unit tested) that does not support printig yet*
 - branch: [observer-pattern](https://github.com/Crix4lis/two-heroes-fight-simulation/tree/observer-pattern); tag: [running-app-observer-pattern](https://github.com/Crix4lis/two-heroes-fight-simulation/tree/running-app-observer-pattern):<br>
 *Contains all code from `base` branch and extends it with printing and logging with usage of Observer Pattern*
 - branch: [mediator-pattern](https://github.com/Crix4lis/two-heroes-fight-simulation/tree/mediator-pattern); tag: [running-app-mediator-pattern](https://github.com/Crix4lis/two-heroes-fight-simulation/tree/running-app-mediator-pattern):<br>
 *Contains all code from `base` branch and extends it with printing and logging with usage of Mediator Pattern*

More branches and approaches to be added...
 
**Current branch:** [observer-pattern](https://github.com/Crix4lis/two-heroes-fight-simulation/tree/observer-pattern)

## WORD OF EXPLANATION
The easiest and most 'web app' solution would be to use some external or self made
event bus but I just want to play with Design Patterns (I might add that solution someday).

### TODO
- Forgot to implement miss ability... 💩
- Fix non existing delegation in MagicShield decorator
