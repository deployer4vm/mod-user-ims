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

/*
USER PAGE
----------------------------------------------------------------
*/
const MyProfile = resolve => {
  require.ensure(["../views/admin/user/myprofile"], () => {
    resolve(require("../views/admin/user/myprofile"));
  });
};
const NotifList = resolve => {
  require.ensure(["../views/admin/user/notiflist"], () => {
    resolve(require("../views/admin/user/notiflist"));
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
if(globals().AppConfig.packageLocal.moduser.auth_template.type==1){
  var authPage = [
    {
      path: "login",
      component: LoginPage2,
      name: "login"
    },
    {
      path: "forgot",
      component: ForgotPasswordPage2,
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
      }
    ]
  }
];
