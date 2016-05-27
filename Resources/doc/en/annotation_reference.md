### @AutoComplete ###

This annotation using select2 jQuery plugin to handling behaviour. This annotation is used inside `@Util` annotation. 

#### Annotation attributes ####

`routeResource` : Route that providing search datas.

`routeCallback` : Route will be called when data is provided on load (see: [initSelection](http://select2.github.io/select2/#documentation))

`targetSelector` : jQuery selector to store data

#### Example usage ####

```lang=php
@Util(
    autoComplete=@AutoComplete(
        routeResource="search", 
        routeCallback="detail", 
        targetSelector="#store_value"
    )
)
```

### @Column ###

This annotation is used inside `@Grid` annotation or on class property of entity or document

#### Example usage ####

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

### @Crud ###

This annotation is required to manipulate crud operation.

#### Annotation attributes ####

`template` : Handle templating for create, read, edit and delete operation (see: `@Template`)

`showFields` : List of entity or document field to show on detail

`modelClass` : FQCN of entity or document class

`form` : FQCN or service name of form to use on create and edit operation

`menu` : Handling menu for current controller (see: `@Menu`)

`allowCreate` : Booelan to allowing create operation, by default is `true`

`allowEdit` : Booelan to allowing edit operation, by default is `true`

`allowShow` : Booelan to allowing show operation, by default is `true`

`allowDelete` : Booelan to allowing delete operation, by default is `true`

#### Example usage ####