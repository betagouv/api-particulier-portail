import { Token } from 'src/authentication/token.type';
import { User } from 'src/authentication/types/user.type';

export type Brand<K, T> = K & { __brand: T };

export type Session = {
  token: Token;
  user: User;
};
