import globals from "@/globals";
import BlankRouterContainer from '@/layout/BlankRouterContainer';
import LayoutBlank from '@/layout/LayoutBlank';
// import globals from "@/globals";

// let authEndpoint = globals().AppConfig.packageLocal.moduser.auth_endpoint[globals().AppConfig.system.mode]['admin'];

/*
AUTH PAGE
----------------------------------------------------------------
*/
const LoginPage = resolve => {
  require.ensure(["../views/admin/auth/loginform"], () => {
    resolve(require("../views/admin/auth/loginform"));
  });
};
const LoginPage2 = resolve => {
  require.ensure(["../views/admin/auth/loginform2"], () => {
    resolve(require("../views/admin/auth/loginform2"));
  });
};
//custom
const LoginPage3 = resolve => {
  require.ensure(["node_modules/../app/MainApp/resources/js/components/moduser/auth/loginform"], () => {
    resolve(require("node_modules/../app/MainApp/resources/js/components/moduser/auth/loginform"));
  });
};


const RegisterPage = resolve => {
    require.ensure(["../views/admin/auth/register"], () => {
        resolve(require("../views/admin/auth/register"));
    });
};
const RegisterPage2 = resolve => {
    require.ensure(["../views/admin/auth/register2"], () => {
        resolve(require("../views/admin/auth/register2"));
    });
};
//custom
const RegisterPage3 = resolve => {
    require.ensure(["node_modules/../app/MainApp/resources/js/components/moduser/auth/register"], () => {
        resolve(require("node_modules/../app/MainApp/resources/js/components/moduser/auth/register"));
    });
};

const ForgotPasswordPage = resolve => {
  require.ensure(["../views/admin/auth/forgotpasswordform"], () => {
    resolve(require("../views/admin/auth/forgotpasswordform"));
  });
};
const ForgotPasswordPage2 = resolve => {
  require.ensure(["../views/admin/auth/forgotpasswordform2"], () => {
    resolve(require("../views/admin/auth/forgotpasswordform2"));
  });
};
//custom
const ForgotPasswordPage3 = resolve => {
  require.ensure(["node_modules/../app/MainApp/resources/js/components/moduser/auth/forgotpasswordform"], () => {
    resolve(require("node_modules/../app/MainApp/resources/js/components/moduser/auth/forgotpasswordform"));
  });
};

/*
USER PAGE
----------------------------------------------------------------
*/
const MyProfile = resolve => {
  require.ensure(["../views/admin/user/myprofile"], () => {
    resolve(require("../views/admin/user/myprofile"));
  });
};
const UserList = resolve => {
  require.ensure(["../views/admin/user/userlist"], () => {
    resolve(require("../views/admin/user/userlist"));
  });
};
const UserView = resolve => {
  require.ensure(["../views/admin/user/userview"], () => {
    resolve(require("../views/admin/user/userview"));
  });
};
const UserForm = resolve => {
  require.ensure(["../views/admin/user/userform"], () => {
    resolve(require("../views/admin/user/userform"));
  });
};

const RoleList = resolve => {
  require.ensure(["../views/admin/role/rolelist"], () => {
    resolve(require("../views/admin/role/rolelist"));
  });
};
const RoleForm = resolve => {
  require.ensure(["../views/admin/role/roleform"], () => {
    resolve(require("../views/admin/role/roleform"));
  });
};

const NotifList = resolve => {
    require.ensure(["../views/admin/user/notiflist"], () => {
      resolve(require("../views/admin/user/notiflist"));
    });
  };
const NotifDetail = resolve => {
    require.ensure(["../views/admin/user/notifdetail"], () => {
        resolve(require("../views/admin/user/notifdetail"));
    });
};

/*
Manage Api 
----------------------------------------------------------------
*/

const ManageApiList = resolve => {
  require.ensure(["../views/admin/manageapi/list"], () => {
    resolve(require("../views/admin/manageapi/list"));
  });
};

const ManageApiForm = resolve => {
  require.ensure(["../views/admin/manageapi/form"], () => {
    resolve(require("../views/admin/manageapi/form"));
  });
};

const ManageApiRoleList = resolve => {
  require.ensure(["../views/admin/manageapi/role/rolelist"], () => {
    resolve(require("../views/admin/manageapi/role/rolelist"));
  });
};

const ManageApiRoleForm = resolve => {
  require.ensure(["../views/admin/manageapi/role/roleform"], () => {
    resolve(require("../views/admin/manageapi/role/roleform"));
  });
};


/*
Broadcast
----------------------------------------------------------------
*/

const BroadcastList = resolve => {
    require.ensure(["../views/admin/broadcast/list"], () => {
        resolve(require("../views/admin/broadcast/list"));
    });
};
const BroadcastForm = resolve => {
    require.ensure(["../views/admin/broadcast/form"], () => {
        resolve(require("../views/admin/broadcast/form"));
    });
};

//style/tampilan auth page
if(globals().AppConfig.packageLocal.moduser.auth_template.type==1){
  var authPage = [
    {
      path: "login",
      component: LoginPage2,
      name: "login"
    },
    {
      path: "register",
      component: RegisterPage2,
      name: "register"
    },
    {
      path: "forgot",
      component: ForgotPasswordPage2,
      name: "forgotpassword"
    }
  ];
}else if(globals().AppConfig.packageLocal.moduser.auth_template.type==2){
  var authPage = [
    {
      path: "login",
      component: LoginPage3,
      name: "login"
    },
    {
      path: "register",
      component: RegisterPage3,
      name: "register"
    },
    {
      path: "forgot",
      component: ForgotPasswordPage3,
      name: "forgotpassword"
    }
  ];
}else{
  var authPage = [
    {
      path: "login",
      component: LoginPage,
      name: "login"
    },
    {
      path: "register",
      component: RegisterPage,
      name: "register"
    },
    {
      path: "forgot",
      component: ForgotPasswordPage,
      name: "forgotpassword"
    }
  ];
}

export default [
  {
    path: globals().AppConfig.endpoint.admin.auth,
    component: LayoutBlank,
    children: authPage
  },
  {
    path: globals().AppConfig.endpoint.admin.moduser,
    component: () => import("@/layout/" + globals().AppConfig.system.web_admin.layout),
    children: [
      {
        path: "myprofile",
        component: MyProfile,
        name: "myprofile"
      },
      {
        path: "notification",
        component: NotifList,
        name: "notification"
      },
      {
        path: "notification/:notifId",
        component: NotifDetail,
        name: "notification.detail"
      },
      {
        path: "list",
        component: UserList,
        name: "user.list"
      },
      {
        path: "add",
        component: UserForm,
        name: "user.add"
      },
      {
        path: "edit/:userId",
        component: UserForm,
        name: "user.edit"
      },
      {
        path: "view/:userId",
        component: UserView,
        name: "user.view"
      },
      {
        path: "broadcast",
        component: BroadcastList,
        name: "broadcast.list"
      },
      {
        path: "broadcast/form",
        component: BroadcastForm,
        name: "broadcast.form"
      },
      {
        path: "role",
        component: BlankRouterContainer,
        children: [
          {
            path: "list",
            component: RoleList,
            name: "role.list"
          },
          {
            path: "add",
            component: RoleForm,
            name: "role.add"
          },
          {
            path: "edit/:roleId",
            component: RoleForm,
            name: "role.edit"
          },
        ]
      },
      {
        path: "manageapi",
        component: ManageApiList,
        name: "manageapi.list"
      },
      {
        path: "manageapi/add",
        component: ManageApiForm,
        name: "manageapi.add"
      },
      {
        path: "manageapi/edit/:userId",
        component: ManageApiForm,
        name: "manageapi.edit"
      },
      {
        path: "manageapirole",
        component: ManageApiRoleList,
        name: "manageapi.role"
      },
      {
        path: "manageapirole/add",
        component: ManageApiRoleForm,
        name: "manageapi.role.add"
      },
      {
        path: "manageapirole/edit/:roleId",
        component: ManageApiRoleForm,
        name: "manageapi.role.edit"
      }
    ]
  }
];
