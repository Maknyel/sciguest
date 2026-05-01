<?php

namespace Database\Seeders;

use App\Models\Activity;
use App\Models\Module;
use App\Models\QuizQuestion;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Sections
        $sections = ['Grade 9 - Acacia', 'Grade 9 - Dao', 'Grade 9 - Banaba', 'Grade 9 - Gmelina'];
        foreach ($sections as $name) {
            Section::create(['name' => $name]);
        }

        // Teacher
        User::create([
            'full_name' => "Ma'am Semillano",
            'username'  => 'teacher',
            'password'  => Hash::make('teacher123'),
            'role'      => 'teacher',
            'status'    => 'approved',
        ]);

        // Modules
        $modulesData = [
            [
                'order' => 1,
                'name' => 'Force and Motion',
                'icon' => '⚙',
                'description' => 'Explore forces, vectors, collisions, and motion experiments.',
                'color' => '#00e5ff',
                'activities' => [
                    ['order'=>1,'name'=>'ROLL, ROLL AND AWAY!','icon'=>'🔵','procedure'=>"Step 1: Prepare the materials.\nStep 2: Set up the ramp at various angles.\nStep 3: Release the ball and measure the distance.\nStep 4: Record your observations.\nStep 5: Repeat for different angles."],
                    ['order'=>2,'name'=>'DROP ME!','icon'=>'🔵','procedure'=>"Step 1: Hold objects at equal heights.\nStep 2: Drop them simultaneously.\nStep 3: Observe which lands first.\nStep 4: Record your findings."],
                    ['order'=>3,'name'=>'YOU RAISE ME UP!','icon'=>'🔵','procedure'=>"Step 1: Set up the pulley system.\nStep 2: Attach different weights.\nStep 3: Measure the force needed.\nStep 4: Calculate mechanical advantage."],
                    ['order'=>4,'name'=>'CURVE ME ON AN INCLINE','icon'=>'🔵','procedure'=>"Step 1: Set up the inclined plane.\nStep 2: Place an object on it.\nStep 3: Measure the angle and force.\nStep 4: Calculate net force components."],
                    ['order'=>5,'name'=>'CURVE A LIKE','icon'=>'⚙','procedure'=>"Step 1: Set up circular motion apparatus.\nStep 2: Vary speed and measure centripetal force.\nStep 3: Record observations."],
                    ['order'=>6,'name'=>'INVESTIGATING MOMENTUM','icon'=>'⚙','procedure'=>"Step 1: Set up two carts on a track.\nStep 2: Push one cart toward the other.\nStep 3: Measure velocities before and after.\nStep 4: Calculate momentum."],
                    ['order'=>7,'name'=>'PLAYING EGG VOLLEYBALL','icon'=>'🟠','procedure'=>"Step 1: Use protective materials to wrap egg.\nStep 2: Drop from various heights.\nStep 3: Observe impulse and momentum."],
                    ['order'=>8,'name'=>'BALLOON ROCKET','icon'=>'🔴','procedure'=>"Step 1: Thread string through a straw.\nStep 2: Inflate balloon without tying.\nStep 3: Release and measure distance."],
                ],
            ],
            [
                'order' => 2,
                'name' => 'Work, Power, and Energy',
                'icon' => '⚡',
                'description' => 'Hands-on activities demonstrating energy transfer and conservation.',
                'color' => '#6c63ff',
                'activities' => [
                    ['order'=>1,'name'=>'LITTLE SHOP OF TOYS','icon'=>'👤','procedure'=>"Step 1: Examine different toys.\nStep 2: Identify types of energy.\nStep 3: Measure work done by each toy."],
                    ['order'=>2,'name'=>'HEP HEP HOORAY!','icon'=>'🔴','procedure'=>"Step 1: Set up pendulum.\nStep 2: Release from different heights.\nStep 3: Measure kinetic and potential energy."],
                    ['order'=>3,'name'=>'BASHING BALL!','icon'=>'🟢','procedure'=>"Step 1: Suspend a heavy ball.\nStep 2: Release and measure energy transfer.\nStep 3: Observe elastic collision."],
                    ['order'=>4,'name'=>'BOUNCY BALLS','icon'=>'🟠','procedure'=>"Step 1: Drop balls from measured height.\nStep 2: Record bounce height.\nStep 3: Calculate energy loss."],
                ],
            ],
            [
                'order' => 3,
                'name' => 'Heat and Thermodynamics',
                'icon' => '🔥',
                'description' => 'Thermodynamics experiments, heat transfer, and efficiency.',
                'color' => '#ff6b35',
                'activities' => [
                    ['order'=>1,'name'=>'HEAT AND INTERNAL ENERGY','icon'=>'✏','procedure'=>"Step 1: Heat water to different temperatures.\nStep 2: Measure heat absorbed.\nStep 3: Calculate specific heat capacity."],
                    ['order'=>2,'name'=>'WHERE DO I BELONG?','icon'=>'🔵🟠','procedure'=>"Step 1: Classify materials as conductors or insulators.\nStep 2: Test each material with heat source.\nStep 3: Record results."],
                    ['order'=>3,'name'=>'THE REVERSE OF IT','icon'=>'🔵','procedure'=>"Step 1: Set up a refrigeration demonstration.\nStep 2: Observe heat pump operation.\nStep 3: Record temperature changes."],
                    ['order'=>4,'name'=>'START THE ENGINE!','icon'=>'🚀','procedure'=>"Step 1: Examine heat engine model.\nStep 2: Measure work output vs heat input.\nStep 3: Calculate efficiency."],
                    ['order'=>5,'name'=>'FILL ME IN','icon'=>'✏','procedure'=>"Step 1: Complete the data table with calculations.\nStep 2: Analyze thermodynamic cycles.\nStep 3: Summarize findings."],
                ],
            ],
            [
                'order' => 4,
                'name' => 'Electricity and Magnetism',
                'icon' => '🔌',
                'description' => 'Circuits, fields, and electromagnetic demonstrations.',
                'color' => '#00ff88',
                'activities' => [
                    ['order'=>1,'name'=>'CIRCUIT BASICS','icon'=>'🔌','procedure'=>"Step 1: Build a simple series circuit.\nStep 2: Measure voltage and current.\nStep 3: Apply Ohm\'s Law."],
                    ['order'=>2,'name'=>'PARALLEL PATHS','icon'=>'⚡','procedure'=>"Step 1: Build a parallel circuit.\nStep 2: Compare current in each branch.\nStep 3: Calculate equivalent resistance."],
                    ['order'=>3,'name'=>'MAGNETIC FIELDS','icon'=>'🧲','procedure'=>"Step 1: Use iron filings around a magnet.\nStep 2: Observe field lines.\nStep 3: Sketch the magnetic field pattern."],
                    ['order'=>4,'name'=>'ELECTROMAGNETIC INDUCTION','icon'=>'🔋','procedure'=>"Step 1: Move magnet through a coil.\nStep 2: Observe galvanometer deflection.\nStep 3: Record induced EMF vs speed."],
                ],
            ],
        ];

        foreach ($modulesData as $moduleData) {
            $activities = $moduleData['activities'];
            unset($moduleData['activities']);

            $module = Module::create($moduleData);

            foreach ($activities as $actData) {
                $actData['module_id'] = $module->id;
                $actData['safety_reminders'] = "Always wear safety goggles.\nHandle equipment with care.\nReport any accidents immediately.\nKeep the workspace clean and tidy.\nDo not eat or drink in the lab.";
                $actData['description'] = $actData['procedure'] ? substr($actData['procedure'], 0, 60) . '...' : '';
                $activity = Activity::create($actData);

                // Add sample quiz questions
                QuizQuestion::create([
                    'activity_id' => $activity->id,
                    'order' => 1,
                    'question' => 'What is the main concept demonstrated in this activity?',
                    'options' => ['Newton\'s Laws', 'Conservation of Energy', 'Ohm\'s Law', 'Thermodynamics'],
                    'correct_answer' => 'Newton\'s Laws',
                ]);
                QuizQuestion::create([
                    'activity_id' => $activity->id,
                    'order' => 2,
                    'question' => 'Which unit is used to measure force?',
                    'options' => ['Joule', 'Newton', 'Watt', 'Pascal'],
                    'correct_answer' => 'Newton',
                ]);
                QuizQuestion::create([
                    'activity_id' => $activity->id,
                    'order' => 3,
                    'question' => 'What does the law of conservation of energy state?',
                    'options' => [
                        'Energy can be created from nothing',
                        'Energy cannot be created or destroyed, only transformed',
                        'Energy always decreases over time',
                        'Energy is always conserved as heat',
                    ],
                    'correct_answer' => 'Energy cannot be created or destroyed, only transformed',
                ]);
            }
        }
    }
}
