<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //获取Faker实例
        $faker = app(\Faker\Generator::class);

        // 头像假数据
        $avatars = [
            'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/s5ehp11z6s.png?imageView2/1/w/200/h/200',
            'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/Lhd1SHqu86.png?imageView2/1/w/200/h/200',
            'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/LOnMrqbHJn.png?imageView2/1/w/200/h/200',
            'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/xAuDMxteQy.png?imageView2/1/w/200/h/200',
            'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/ZqM7iaP4CR.png?imageView2/1/w/200/h/200',
            'https://iocaffcdn.phphub.org/uploads/images/201710/14/1/NDnzMutoxX.png?imageView2/1/w/200/h/200',
        ];

        //生成数据集合
        $user = factory(\App\Models\User::class)
            ->times(10)
            ->make()
            ->each(function($user, $index) use ($faker,$avatars) {
                //从头像数组中随机获取一个值并且赋值
                $user->avatar = $faker->randomElement($avatars);
            });

        //让隐藏字段可见，并且讲数据集合转为数组
        $user_array = $user->makeVisible(['password', 'remember_token'])->toArray();

        //插入数据库
        \App\Models\User::insert($user_array);

        //单独处理第一个用户
        $user = \App\Models\User::find(1);
        $user->name = 'lichenhui';
        $user->email = '627893025@qq.com';
        $user->avatar = 'http://larabbs.lch.test/uploads/images/avatars/201812/05/1_1544003569_pVWNzsziIS.jpeg';
        $user->save();
        //1号为站长
        $user->assignRole('Founder');

        $user2 = \App\Models\User::find(2);
        $user2->name = 'lch';
        $user2->email = '414650923@qq.com';
        $user2->avatar = 'http://larabbs.lch.test/uploads/images/avatars/201812/05/1_1544003569_pVWNzsziIS.jpeg';
        $user2->save();
        //2号为管理员
        $user2->assignRole('Maintainer');


    }
}
