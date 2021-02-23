import { Brand } from 'src/types';

export type UserEmail = Brand<string, 'UserEmail'>;

export type UserId = Brand<string, 'UserId'>;

export type User = {
  firstname: string;
  lastname: string;
  email: UserEmail;
  id: UserId;
};
