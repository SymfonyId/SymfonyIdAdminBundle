### @AutoComplete ###

This annotation using select2 jQuery plugin to handling behaviour. This annotation is used inside `@Util` annotation. 

+ `routeResource` : Route that providing search datas.

+ `routeCallback` : Route will be called when data is provided on load (see: [initSelection](http://select2.github.io/select2/#documentation))

+ `targetSelector` : jQuery selector to store data

```lang=php
@Util(autoComplete=@AutoComplete(routeResource="search", routeCallback="detail", targetSelector="#store_value"))
```

### @Column ###

This annotation is used inside `@Grid` annotation or on class property of entity or document

```lang=php
@Grid(column=@Column({"column1", "column2", "column3"}))
```

```lang=php
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @Column()
     * @ORM\Column(name="username", type="string", length=77)
     */
    protected $username;
}
```